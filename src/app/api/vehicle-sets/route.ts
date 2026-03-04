import { NextRequest, NextResponse } from "next/server";
import { getDb } from "@/lib/db";

export async function GET() {
  try {
    const db = getDb();
    const sets = db.prepare(
      "SELECT id, truck_id as truckId, trailer_id as trailerId, category FROM vehicle_sets"
    ).all();
    return NextResponse.json(sets);
  } catch (error) {
    console.error("GET /api/vehicle-sets error:", error);
    return NextResponse.json({ error: "Veri alınamadı" }, { status: 500 });
  }
}

export async function POST(req: NextRequest) {
  try {
    const db = getDb();
    const body = await req.json();
    const id = body.id || `set_${body.category}_${Date.now()}`;

    db.prepare("INSERT INTO vehicle_sets (id, truck_id, trailer_id, category) VALUES (?, ?, ?, ?)").run(
      id, body.truckId, body.trailerId, body.category || "asmira"
    );

    return NextResponse.json({ id }, { status: 201 });
  } catch (error) {
    console.error("POST /api/vehicle-sets error:", error);
    return NextResponse.json({ error: "Kayıt oluşturulamadı" }, { status: 500 });
  }
}

export async function DELETE(req: NextRequest) {
  try {
    const db = getDb();
    const { id } = await req.json();
    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    db.prepare("DELETE FROM vehicle_sets WHERE id = ?").run(id);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/vehicle-sets error:", error);
    return NextResponse.json({ error: "Silme başarısız" }, { status: 500 });
  }
}
