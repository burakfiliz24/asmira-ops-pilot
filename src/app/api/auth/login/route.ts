import { NextRequest, NextResponse } from "next/server";
import { getDb } from "@/lib/db";

export async function POST(req: NextRequest) {
  try {
    const db = getDb();
    const { username, password } = await req.json();

    if (!username || !password) {
      return NextResponse.json({ error: "Kullanıcı adı ve şifre gerekli" }, { status: 400 });
    }

    const user = db.prepare(
      "SELECT id, username, name, role FROM users WHERE LOWER(username) = LOWER(?) AND password = ?"
    ).get(username, password) as { id: string; username: string; name: string; role: string } | undefined;

    if (!user) {
      return NextResponse.json({ error: "Kullanıcı adı veya şifre hatalı" }, { status: 401 });
    }

    return NextResponse.json(user);
  } catch (error) {
    console.error("POST /api/auth/login error:", error);
    return NextResponse.json({ error: "Giriş başarısız" }, { status: 500 });
  }
}
