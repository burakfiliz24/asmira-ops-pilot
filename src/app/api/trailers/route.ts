import { NextRequest, NextResponse } from "next/server";
import { getDb } from "@/lib/db";

export async function GET() {
  try {
    const db = getDb();
    const trailers = db.prepare("SELECT id, plate, category FROM trailers ORDER BY created_at").all();

    const stmt = db.prepare(
      "SELECT doc_type as type, label, file_name as fileName, file_path as filePath, expiry_date as expiryDate FROM vehicle_documents WHERE owner_id = ? AND owner_type = 'trailer'"
    );

    const result = (trailers as { id: string; plate: string; category: string }[]).map((t) => ({
      ...t,
      documents: stmt.all(t.id),
    }));

    return NextResponse.json(result);
  } catch (error) {
    console.error("GET /api/trailers error:", error);
    return NextResponse.json({ error: "Veri alınamadı" }, { status: 500 });
  }
}

export async function POST(req: NextRequest) {
  try {
    const db = getDb();
    const body = await req.json();
    const id = body.id || `trailer_${Date.now()}`;

    db.prepare("INSERT INTO trailers (id, plate, category) VALUES (?, ?, ?)").run(
      id, body.plate, body.category || "asmira"
    );

    if (body.documents && Array.isArray(body.documents)) {
      const insertDoc = db.prepare(
        "INSERT INTO vehicle_documents (owner_id, owner_type, doc_type, label) VALUES (?, 'trailer', ?, ?)"
      );
      for (const doc of body.documents) {
        insertDoc.run(id, doc.type, doc.label);
      }
    }

    return NextResponse.json({ id }, { status: 201 });
  } catch (error) {
    console.error("POST /api/trailers error:", error);
    return NextResponse.json({ error: "Kayıt oluşturulamadı" }, { status: 500 });
  }
}

export async function PUT(req: NextRequest) {
  try {
    const db = getDb();
    const { id, plate } = await req.json();
    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    db.prepare("UPDATE trailers SET plate = ? WHERE id = ?").run(plate, id);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("PUT /api/trailers error:", error);
    return NextResponse.json({ error: "Güncelleme başarısız" }, { status: 500 });
  }
}

export async function DELETE(req: NextRequest) {
  try {
    const db = getDb();
    const { id } = await req.json();
    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    db.prepare("DELETE FROM vehicle_documents WHERE owner_id = ? AND owner_type = 'trailer'").run(id);
    db.prepare("DELETE FROM trailers WHERE id = ?").run(id);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/trailers error:", error);
    return NextResponse.json({ error: "Silme başarısız" }, { status: 500 });
  }
}
