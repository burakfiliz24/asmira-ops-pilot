/**
 * PHP Deployment Build Script
 * 
 * Bu script Next.js uygulamasını statik HTML/CSS/JS olarak derler.
 * Backend tamamen PHP ile çalışır (server/ dizini).
 * 
 * Kullanım: npm run build:php
 */

const { execSync } = require("child_process");
const fs = require("fs");
const path = require("path");

const ROOT = path.join(__dirname, "..");
const NEXT_DIR = path.join(ROOT, ".next");
const BACKUP_DIR = path.join(ROOT, "_build-backup");

// Dinamik route'lar Turbopack static export ile uyumsuz - geçici olarak taşınır
const DIRS_TO_MOVE = [
  path.join(ROOT, "src", "app", "(app)", "vehicle-documents", "[id]"),
  path.join(ROOT, "src", "app", "(app)", "petitions", "custom", "[slug]"),
];

console.log("🔧 PHP deployment build başlatılıyor...\n");

// 1. .next cache temizle
if (fs.existsSync(NEXT_DIR)) {
  console.log("🧹 .next cache temizleniyor...");
  fs.rmSync(NEXT_DIR, { recursive: true, force: true });
}

// 2. Önceki backup varsa temizle
if (fs.existsSync(BACKUP_DIR)) {
  fs.rmSync(BACKUP_DIR, { recursive: true, force: true });
}
fs.mkdirSync(BACKUP_DIR, { recursive: true });

// 3. Sorunlu dizinleri geçici olarak taşı
const movedDirs = [];
for (const dir of DIRS_TO_MOVE) {
  if (fs.existsSync(dir)) {
    const backupPath = path.join(BACKUP_DIR, path.basename(dir));
    console.log(`📦 Taşınıyor: ${path.relative(ROOT, dir)}`);
    fs.renameSync(dir, backupPath);
    movedDirs.push({ original: dir, backup: backupPath });
  }
}

try {
  // 2. Static export ile build
  console.log("🏗️  Next.js static export çalışıyor...\n");
  execSync("npx next build", {
    stdio: "inherit",
    env: { ...process.env, STATIC_EXPORT: "true" },
    cwd: path.join(__dirname, ".."),
  });

  console.log("\n✅ Build başarılı! Dosyalar: out/ dizininde");
  console.log("\n📋 Deployment adımları:");
  console.log("   1. out/ içindeki tüm dosyaları → public_html/ klasörüne yükleyin");
  console.log("   2. server/api/ içindeki PHP dosyalarını → public_html/api/ klasörüne yükleyin");
  console.log("   3. server/.htaccess dosyasını → public_html/.htaccess olarak yükleyin");
  console.log("   4. server/api/config.php dosyasında DB bilgilerini güncelleyin");
  console.log("   5. http://siteniz.com/api/init-db adresini ziyaret edin (tabloları oluşturur)\n");

} catch (error) {
  console.error("\n❌ Build hatası!");
  process.exit(1);
} finally {
  // Taşınan dizinleri geri yükle
  for (const { original, backup } of movedDirs) {
    if (fs.existsSync(backup)) {
      console.log(`📦 Geri yükleniyor: ${path.relative(ROOT, original)}`);
      fs.renameSync(backup, original);
    }
  }
  // Backup dizinini temizle
  if (fs.existsSync(BACKUP_DIR)) {
    fs.rmSync(BACKUP_DIR, { recursive: true, force: true });
  }
}
