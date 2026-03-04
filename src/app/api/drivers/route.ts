import { NextRequest, NextResponse } from "next/server";
import { getDb } from "@/lib/db";

export async function GET() {
  try {
    const db = getDb();
    const drivers = db.prepare("SELECT id, name, tc_no as tcNo, phone FROM drivers ORDER BY created_at").all();

    const stmt = db.prepare(
      "SELECT doc_type as type, label, file_name as fileName, file_path as filePath, expiry_date as expiryDate FROM driver_documents WHERE driver_id = ?"
    );

    const result = (drivers as { id: string; name: string; tcNo: string; phone: string }[]).map((d) => ({
      ...d,
      documents: stmt.all(d.id),
    }));

    return NextResponse.json(result);
  } catch (error) {
    console.error("GET /api/drivers error:", error);
    return NextResponse.json({ error: "Veri alınamadı" }, { status: 500 });
  }
}

export async function POST(req: NextRequest) {
  try {
    const db = getDb();
    const body = await req.json();
    const id = body.id || `driver_${Date.now()}`;

    db.prepare("INSERT INTO drivers (id, name, tc_no, phone) VALUES (?, ?, ?, ?)").run(
      id, body.name, body.tcNo, body.phone
    );

    if (body.documents && Array.isArray(body.documents)) {
      const insertDoc = db.prepare(
        "INSERT INTO driver_documents (driver_id, doc_type, label) VALUES (?, ?, ?)"
      );
      for (const doc of body.documents) {
        insertDoc.run(id, doc.type, doc.label);
      }
    }

    return NextResponse.json({ id }, { status: 201 });
  } catch (error) {
    console.error("POST /api/drivers error:", error);
    return NextResponse.json({ error: "Kayıt oluşturulamadı" }, { status: 500 });
  }
}

export async function PUT(req: NextRequest) {
  try {
    const db = getDb();
    const { id, name, tcNo, phone } = await req.json();
    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    db.prepare("UPDATE drivers SET name = ?, tc_no = ?, phone = ? WHERE id = ?").run(name, tcNo, phone, id);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("PUT /api/drivers error:", error);
    return NextResponse.json({ error: "Güncelleme başarısız" }, { status: 500 });
  }
}

export async function DELETE(req: NextRequest) {
  try {
    const db = getDb();
    const { id } = await req.json();
    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    db.prepare("DELETE FROM driver_documents WHERE driver_id = ?").run(id);
    db.prepare("DELETE FROM drivers WHERE id = ?").run(id);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/drivers error:", error);
    return NextResponse.json({ error: "Silme başarısız" }, { status: 500 });
  }
}
