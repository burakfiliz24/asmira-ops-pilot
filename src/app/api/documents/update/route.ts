import { NextRequest, NextResponse } from "next/server";
import { getDb } from "@/lib/db";
import path from "path";
import fs from "fs";

const UPLOAD_DIR = path.join(process.cwd(), "data", "uploads");

// Evrak tarih güncelleme
export async function PUT(req: NextRequest) {
  try {
    const db = getDb();
    const { ownerId, ownerType, docType, expiryDate } = await req.json();

    if (!ownerId || !ownerType || !docType) {
      return NextResponse.json({ error: "Eksik parametreler" }, { status: 400 });
    }

    if (ownerType === "driver") {
      db.prepare(
        "UPDATE driver_documents SET expiry_date = ?, updated_at = datetime('now') WHERE driver_id = ? AND doc_type = ?"
      ).run(expiryDate || null, ownerId, docType);
    } else {
      db.prepare(
        "UPDATE vehicle_documents SET expiry_date = ?, updated_at = datetime('now') WHERE owner_id = ? AND owner_type = ? AND doc_type = ?"
      ).run(expiryDate || null, ownerId, ownerType, docType);
    }

    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("PUT /api/documents/update error:", error);
    return NextResponse.json({ error: "Güncelleme başarısız" }, { status: 500 });
  }
}

// Evrak silme (dosya + kayıt sıfırlama)
export async function DELETE(req: NextRequest) {
  try {
    const db = getDb();
    const { ownerId, ownerType, docType } = await req.json();

    if (!ownerId || !ownerType || !docType) {
      return NextResponse.json({ error: "Eksik parametreler" }, { status: 400 });
    }

    // Dosyayı diskten sil
    const ext = ".pdf";
    const filePath = path.join(UPLOAD_DIR, ownerType, ownerId, `${docType}${ext}`);
    if (fs.existsSync(filePath)) {
      fs.unlinkSync(filePath);
    }

    // Veritabanında sıfırla
    if (ownerType === "driver") {
      db.prepare(
        "UPDATE driver_documents SET file_name = NULL, file_path = NULL, expiry_date = NULL, updated_at = datetime('now') WHERE driver_id = ? AND doc_type = ?"
      ).run(ownerId, docType);
    } else {
      db.prepare(
        "UPDATE vehicle_documents SET file_name = NULL, file_path = NULL, expiry_date = NULL, updated_at = datetime('now') WHERE owner_id = ? AND owner_type = ? AND doc_type = ?"
      ).run(ownerId, ownerType, docType);
    }

    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/documents/update error:", error);
    return NextResponse.json({ error: "Silme başarısız" }, { status: 500 });
  }
}
