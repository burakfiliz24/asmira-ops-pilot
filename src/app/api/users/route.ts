import { NextRequest, NextResponse } from "next/server";
import { getDb } from "@/lib/db";

export async function GET() {
  try {
    const db = getDb();
    const users = db.prepare("SELECT id, username, password, name, role FROM users ORDER BY created_at").all();
    return NextResponse.json(users);
  } catch (error) {
    console.error("GET /api/users error:", error);
    return NextResponse.json({ error: "Veri alınamadı" }, { status: 500 });
  }
}

export async function POST(req: NextRequest) {
  try {
    const db = getDb();
    const body = await req.json();
    const id = `user_${Date.now()}`;

    const existing = db.prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(?)").get(body.username);
    if (existing) {
      return NextResponse.json({ error: "Bu kullanıcı adı zaten mevcut" }, { status: 409 });
    }

    db.prepare("INSERT INTO users (id, username, password, name, role) VALUES (?, ?, ?, ?, ?)").run(
      id, body.username, body.password, body.name, body.role || "user"
    );

    return NextResponse.json({ id, ...body }, { status: 201 });
  } catch (error) {
    console.error("POST /api/users error:", error);
    return NextResponse.json({ error: "Kullanıcı oluşturulamadı" }, { status: 500 });
  }
}

export async function PUT(req: NextRequest) {
  try {
    const db = getDb();
    const { id, ...updates } = await req.json();
    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    const fields: string[] = [];
    const values: unknown[] = [];

    for (const [key, val] of Object.entries(updates)) {
      if (["username", "password", "name", "role"].includes(key)) {
        fields.push(`${key} = ?`);
        values.push(val);
      }
    }

    if (fields.length === 0) return NextResponse.json({ error: "Güncellenecek alan yok" }, { status: 400 });
    values.push(id);

    db.prepare(`UPDATE users SET ${fields.join(", ")} WHERE id = ?`).run(...values);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("PUT /api/users error:", error);
    return NextResponse.json({ error: "Güncelleme başarısız" }, { status: 500 });
  }
}

export async function DELETE(req: NextRequest) {
  try {
    const db = getDb();
    const { id } = await req.json();
    if (!id) return NextResponse.json({ error: "id gerekli" }, { status: 400 });

    // Son admin silinemez
    const admins = db.prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin'").get() as { count: number };
    const userToDelete = db.prepare("SELECT role FROM users WHERE id = ?").get(id) as { role: string } | undefined;

    if (userToDelete?.role === "admin" && admins.count <= 1) {
      return NextResponse.json({ error: "Son yönetici silinemez" }, { status: 400 });
    }

    db.prepare("DELETE FROM users WHERE id = ?").run(id);
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/users error:", error);
    return NextResponse.json({ error: "Silme başarısız" }, { status: 500 });
  }
}
