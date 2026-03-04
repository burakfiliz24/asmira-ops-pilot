import { NextRequest, NextResponse } from "next/server";
import { getDb } from "@/lib/db";

export async function GET() {
  try {
    const db = getDb();
    const operations = db.prepare(`
      SELECT id, vessel_name as vesselName, vessel_type as vesselType, imo_number as imoNumber, quantity, unit,
             loading_place as loadingPlace, port, date, status,
             driver_name as driverName, driver_phone as driverPhone, agent_note as agentNote
      FROM operations ORDER BY date DESC
    `).all();
    return NextResponse.json(operations);
  } catch (error) {
    console.error("GET /api/operations error:", error);
    return NextResponse.json({ error: "Veri alınamadı" }, { status: 500 });
  }
}

export async function POST(req: NextRequest) {
  try {
    const db = getDb();
    const body = await req.json();
    const id = body.id || `op_${Date.now()}`;

    db.prepare(`
      INSERT INTO operations (id, vessel_name, vessel_type, imo_number, quantity, unit, loading_place, port, date, status, driver_name, driver_phone, agent_note)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `).run(
      id, body.vesselName, body.vesselType || "ship", body.imoNumber || null, body.quantity, body.unit || "MT",
      body.loadingPlace || null, body.port, body.date, body.status || "planned",
      body.driverName || "", body.driverPhone || "", body.agentNote || ""
    );

    return NextResponse.json({ id, ...body }, { status: 201 });
  } catch (error) {
    console.error("POST /api/operations error:", error);
    return NextResponse.json({ error: "Kayıt oluşturulamadı" }, { status: 500 });
  }
}

export async function PUT(req: NextRequest) {
  try {
    const db = getDb();
    const body = await req.json();
    const { id, ...updates } = body;

    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    const fields: string[] = [];
    const values: unknown[] = [];

    const fieldMap: Record<string, string> = {
      vesselName: "vessel_name", vesselType: "vessel_type", imoNumber: "imo_number", quantity: "quantity",
      unit: "unit", loadingPlace: "loading_place", port: "port", date: "date",
      status: "status", driverName: "driver_name", driverPhone: "driver_phone",
      agentNote: "agent_note",
    };

    for (const [key, val] of Object.entries(updates)) {
      if (fieldMap[key]) {
        fields.push(`${fieldMap[key]} = ?`);
        values.push(val);
      }
    }

    if (fields.length === 0) return NextResponse.json({ error: "Güncellenecek alan yok" }, { status: 400 });

    fields.push("updated_at = datetime('now')");
    values.push(id);

    db.prepare(`UPDATE operations SET ${fields.join(", ")} WHERE id = ?`).run(...values);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("PUT /api/operations error:", error);
    return NextResponse.json({ error: "Güncelleme başarısız" }, { status: 500 });
  }
}

export async function DELETE(req: NextRequest) {
  try {
    const db = getDb();
    const { id } = await req.json();
    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    db.prepare("DELETE FROM operations WHERE id = ?").run(id);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/operations error:", error);
    return NextResponse.json({ error: "Silme başarısız" }, { status: 500 });
  }
}
