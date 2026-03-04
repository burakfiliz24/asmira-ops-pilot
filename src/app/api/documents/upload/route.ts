import { NextRequest, NextResponse } from "next/server";
import { getDb } from "@/lib/db";
import path from "path";
import fs from "fs";

const UPLOAD_DIR = path.join(process.cwd(), "data", "uploads");

export async function POST(req: NextRequest) {
  try {
    const formData = await req.formData();
    const file = formData.get("file") as File | null;
    const ownerId = formData.get("ownerId") as string;
    const ownerType = formData.get("ownerType") as string; // 'truck' | 'trailer' | 'driver'
    const docType = formData.get("docType") as string;
    const expiryDate = formData.get("expiryDate") as string | null;

    if (!file || !ownerId || !ownerType || !docType) {
      return NextResponse.json({ error: "Eksik parametreler" }, { status: 400 });
    }

    // Klasör oluştur
    const ownerDir = path.join(UPLOAD_DIR, ownerType, ownerId);
    if (!fs.existsSync(ownerDir)) {
      fs.mkdirSync(ownerDir, { recursive: true });
    }

    // Dosyayı kaydet
    const ext = path.extname(file.name) || ".pdf";
    const safeFileName = `${docType}${ext}`;
    const filePath = path.join(ownerDir, safeFileName);
    const relativePath = path.join("uploads", ownerType, ownerId, safeFileName);

    const buffer = Buffer.from(await file.arrayBuffer());
    fs.writeFileSync(filePath, buffer);

    // Veritabanını güncelle
    const db = getDb();
    const table = ownerType === "driver" ? "driver_documents" : "vehicle_documents";
    const ownerCol = ownerType === "driver" ? "driver_id" : "owner_id";

    if (ownerType === "driver") {
      const existing = db.prepare(
        `SELECT id FROM ${table} WHERE ${ownerCol} = ? AND doc_type = ?`
      ).get(ownerId, docType);

      if (existing) {
        db.prepare(
          `UPDATE ${table} SET file_name = ?, file_path = ?, expiry_date = ?, updated_at = datetime('now') WHERE ${ownerCol} = ? AND doc_type = ?`
        ).run(file.name, relativePath, expiryDate || null, ownerId, docType);
      } else {
        db.prepare(
          `INSERT INTO ${table} (${ownerCol}, doc_type, label, file_name, file_path, expiry_date) VALUES (?, ?, ?, ?, ?, ?)`
        ).run(ownerId, docType, docType, file.name, relativePath, expiryDate || null);
      }
    } else {
      const existing = db.prepare(
        `SELECT id FROM ${table} WHERE ${ownerCol} = ? AND owner_type = ? AND doc_type = ?`
      ).get(ownerId, ownerType, docType);

      if (existing) {
        db.prepare(
          `UPDATE ${table} SET file_name = ?, file_path = ?, expiry_date = ?, updated_at = datetime('now') WHERE ${ownerCol} = ? AND owner_type = ? AND doc_type = ?`
        ).run(file.name, relativePath, expiryDate || null, ownerId, ownerType, docType);
      } else {
        db.prepare(
          `INSERT INTO ${table} (${ownerCol}, owner_type, doc_type, label, file_name, file_path, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)`
        ).run(ownerId, ownerType, docType, docType, file.name, relativePath, expiryDate || null);
      }
    }

    return NextResponse.json({ success: true, fileName: file.name, filePath: relativePath });
  } catch (error) {
    console.error("POST /api/documents/upload error:", error);
    return NextResponse.json({ error: "Dosya yüklenemedi" }, { status: 500 });
  }
}
