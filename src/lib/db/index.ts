import Database from "better-sqlite3";
import path from "path";
import fs from "fs";

const DB_DIR = path.join(process.cwd(), "data");
const DB_PATH = path.join(DB_DIR, "asmira.db");

let _db: Database.Database | null = null;

export function getDb(): Database.Database {
  if (_db) return _db;

  // data klasörünü oluştur
  if (!fs.existsSync(DB_DIR)) {
    fs.mkdirSync(DB_DIR, { recursive: true });
  }

  _db = new Database(DB_PATH);
  _db.pragma("journal_mode = WAL");
  _db.pragma("foreign_keys = ON");

  initializeSchema(_db);
  return _db;
}

function initializeSchema(db: Database.Database) {
  db.exec(`
    -- Kullanıcılar
    CREATE TABLE IF NOT EXISTS users (
      id TEXT PRIMARY KEY,
      username TEXT UNIQUE NOT NULL,
      password TEXT NOT NULL,
      name TEXT NOT NULL,
      role TEXT NOT NULL DEFAULT 'user',
      created_at TEXT DEFAULT (datetime('now'))
    );

    -- Çekiciler
    CREATE TABLE IF NOT EXISTS trucks (
      id TEXT PRIMARY KEY,
      plate TEXT NOT NULL,
      category TEXT NOT NULL DEFAULT 'asmira',
      created_at TEXT DEFAULT (datetime('now'))
    );

    -- Dorseler
    CREATE TABLE IF NOT EXISTS trailers (
      id TEXT PRIMARY KEY,
      plate TEXT NOT NULL,
      category TEXT NOT NULL DEFAULT 'asmira',
      created_at TEXT DEFAULT (datetime('now'))
    );

    -- Araç Setleri (Çekici + Dorse eşleştirme)
    CREATE TABLE IF NOT EXISTS vehicle_sets (
      id TEXT PRIMARY KEY,
      truck_id TEXT NOT NULL,
      trailer_id TEXT NOT NULL,
      category TEXT NOT NULL DEFAULT 'asmira',
      FOREIGN KEY (truck_id) REFERENCES trucks(id) ON DELETE CASCADE,
      FOREIGN KEY (trailer_id) REFERENCES trailers(id) ON DELETE CASCADE
    );

    -- Araç Evrakları (çekici + dorse)
    CREATE TABLE IF NOT EXISTS vehicle_documents (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      owner_id TEXT NOT NULL,
      owner_type TEXT NOT NULL CHECK(owner_type IN ('truck', 'trailer')),
      doc_type TEXT NOT NULL,
      label TEXT NOT NULL,
      file_name TEXT,
      file_path TEXT,
      expiry_date TEXT,
      updated_at TEXT DEFAULT (datetime('now'))
    );

    -- Şoförler
    CREATE TABLE IF NOT EXISTS drivers (
      id TEXT PRIMARY KEY,
      name TEXT NOT NULL,
      tc_no TEXT NOT NULL,
      phone TEXT NOT NULL,
      created_at TEXT DEFAULT (datetime('now'))
    );

    -- Şoför Evrakları
    CREATE TABLE IF NOT EXISTS driver_documents (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      driver_id TEXT NOT NULL,
      doc_type TEXT NOT NULL,
      label TEXT NOT NULL,
      file_name TEXT,
      file_path TEXT,
      expiry_date TEXT,
      updated_at TEXT DEFAULT (datetime('now')),
      FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE CASCADE
    );

    -- Operasyonlar
    CREATE TABLE IF NOT EXISTS operations (
      id TEXT PRIMARY KEY,
      vessel_name TEXT NOT NULL,
      vessel_type TEXT NOT NULL DEFAULT 'ship',
      imo_number TEXT,
      quantity REAL NOT NULL DEFAULT 0,
      unit TEXT NOT NULL DEFAULT 'MT',
      loading_place TEXT,
      port TEXT NOT NULL,
      date TEXT NOT NULL,
      status TEXT NOT NULL DEFAULT 'planned',
      driver_name TEXT DEFAULT '',
      driver_phone TEXT DEFAULT '',
      agent_note TEXT DEFAULT '',
      created_at TEXT DEFAULT (datetime('now')),
      updated_at TEXT DEFAULT (datetime('now'))
    );

    -- Dilekçe Kategorileri
    CREATE TABLE IF NOT EXISTS petition_categories (
      id TEXT PRIMARY KEY,
      title TEXT NOT NULL,
      description TEXT DEFAULT '',
      icon TEXT DEFAULT 'FileText',
      slug TEXT NOT NULL
    );

    -- Dilekçe Şablonları
    CREATE TABLE IF NOT EXISTS petition_templates (
      id TEXT PRIMARY KEY,
      short_name TEXT NOT NULL,
      name TEXT NOT NULL,
      default_text TEXT DEFAULT '',
      category TEXT NOT NULL,
      is_default INTEGER DEFAULT 0,
      created_at INTEGER
    );
  `);

  // Migration: vessel_type kolonu ekle (mevcut DB'ler için)
  try {
    db.prepare("ALTER TABLE operations ADD COLUMN vessel_type TEXT NOT NULL DEFAULT 'ship'").run();
  } catch {
    // Kolon zaten varsa hata verir, yoksay
  }

  // Varsayılan admin kullanıcısı
  const existingAdmin = db.prepare("SELECT id FROM users WHERE username = ?").get("asmira");
  if (!existingAdmin) {
    db.prepare("INSERT INTO users (id, username, password, name, role) VALUES (?, ?, ?, ?, ?)").run(
      "user_1", "asmira", "123", "Asmira Operasyon", "admin"
    );
  }
}
