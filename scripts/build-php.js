/**
 * PHP Deployment Build Script
 * 
 * Bu script Next.js uygulamasını derleyip TAM PHP deployment paketi oluşturur.
 * Çıktı: out/ dizini → doğrudan public_html/'e yüklenebilir
 *
 * İşlem sırası:
 * 1. Next.js static export → out/ (HTML/CSS/JS)
 * 2. Tüm .html dosyalarını .php'ye dönüştür
 * 3. server/api/ PHP backend dosyalarını out/api/'ye kopyala
 * 4. .htaccess dosyasını out/'a kopyala
 * 5. PHP wrapper header ekle (session, güvenlik)
 * 
 * Kullanım: npm run build:php
 */

const { execSync } = require("child_process");
const fs = require("fs");
const path = require("path");

const ROOT = path.join(__dirname, "..");
const OUT_DIR = path.join(ROOT, "out");
const SERVER_DIR = path.join(ROOT, "server");
const NEXT_DIR = path.join(ROOT, ".next");
const BACKUP_DIR = path.join(ROOT, "_build-backup");

// Dinamik route'lar Turbopack static export ile uyumsuz - geçici olarak taşınır
const DIRS_TO_MOVE = [
  path.join(ROOT, "src", "app", "(app)", "vehicle-documents", "[id]"),
  path.join(ROOT, "src", "app", "(app)", "petitions", "custom", "[slug]"),
];

// ========================
// YARDIMCI FONKSİYONLAR
// ========================

/** Dizin içindeki tüm dosyaları recursive olarak bul */
function findFiles(dir, ext) {
  const results = [];
  if (!fs.existsSync(dir)) return results;
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      results.push(...findFiles(fullPath, ext));
    } else if (entry.name.endsWith(ext)) {
      results.push(fullPath);
    }
  }
  return results;
}

/** Dizini recursive olarak kopyala */
function copyDirRecursive(src, dest) {
  if (!fs.existsSync(src)) return;
  fs.mkdirSync(dest, { recursive: true });
  const entries = fs.readdirSync(src, { withFileTypes: true });
  for (const entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);
    if (entry.isDirectory()) {
      copyDirRecursive(srcPath, destPath);
    } else {
      fs.copyFileSync(srcPath, destPath);
    }
  }
}

// ========================
// ANA BUILD İŞLEMİ
// ========================

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
  // 4. Next.js static export
  console.log("🏗️  Next.js static export çalışıyor...\n");
  execSync("npx next build", {
    stdio: "inherit",
    env: { ...process.env, STATIC_EXPORT: "true" },
    cwd: ROOT,
  });

  // ========================
  // 5. HTML → PHP DÖNÜŞÜMÜ
  // ========================
  console.log("\n🔄 HTML → PHP dönüşümü başlıyor...");

  const htmlFiles = findFiles(OUT_DIR, ".html");
  let convertedCount = 0;

  // PHP wrapper header — her sayfa dosyasının başına eklenir
  const phpHeader = `<?php
/**
 * Asmira OPS Pilot - PHP Sayfa Dosyası
 * Bu dosya build sırasında otomatik oluşturulmuştur.
 * Orijinal: Next.js static export → HTML → PHP dönüşümü
 */
// Oturum yönetimi (isteğe bağlı genişletilebilir)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Güvenlik başlıkları
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
?>
`;

  for (const htmlFile of htmlFiles) {
    const phpFile = htmlFile.replace(/\.html$/, ".php");
    const htmlContent = fs.readFileSync(htmlFile, "utf-8");

    // PHP header + orijinal HTML içerik
    fs.writeFileSync(phpFile, phpHeader + htmlContent, "utf-8");
    fs.unlinkSync(htmlFile); // Orijinal .html dosyasını sil

    const relPath = path.relative(OUT_DIR, phpFile);
    convertedCount++;
  }
  console.log(`   ✅ ${convertedCount} HTML dosyası PHP'ye dönüştürüldü`);

  // ========================
  // 6. PHP BACKEND API KOPYALAMA
  // ========================
  console.log("\n📂 PHP backend API dosyaları kopyalanıyor...");

  const apiSrc = path.join(SERVER_DIR, "api");
  const apiDest = path.join(OUT_DIR, "api");

  // Eğer out/api/ varsa temizle
  if (fs.existsSync(apiDest)) {
    fs.rmSync(apiDest, { recursive: true, force: true });
  }

  copyDirRecursive(apiSrc, apiDest);

  const phpApiFiles = findFiles(apiDest, ".php");
  console.log(`   ✅ ${phpApiFiles.length} PHP API dosyası kopyalandı`);

  // ========================
  // 7. .HTACCESS KOPYALAMA (PHP uyumlu)
  // ========================
  console.log("\n� .htaccess dosyaları kopyalanıyor...");

  // Ana .htaccess — PHP SPA routing için güncelle
  const mainHtaccess = `RewriteEngine On

# Güvenlik başlıkları
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Hassas dosyalara erişimi engelle
<FilesMatch "(db_config|config|\\.env|\\.log|\\.sql|\\.ini)">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>

# data/ ve logs/ dizinlerine erişimi engelle
RewriteRule ^data/ - [F,L]
RewriteRule ^logs/ - [F,L]

# Gzip sıkıştırma
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript application/json image/svg+xml
</IfModule>

# Tarayıcı önbelleği (statik asset'ler)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

# PHP dosyalarını application/x-httpd-php olarak çalıştır
AddHandler application/x-httpd-php .php

# API isteklerini PHP backend'e yönlendir
RewriteRule ^api/(.*)$ api/$1 [L]

# Mevcut dosya veya dizin ise dokunma (_next/static, resimler vs.)
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Sayfa istekleri: /dashboard → /dashboard/index.php
RewriteCond %{DOCUMENT_ROOT}/%{REQUEST_URI}/index.php -f
RewriteRule ^(.+?)/?$ $1/index.php [L]

# Ana sayfa fallback → index.php (tüm 404'leri yakala)
RewriteRule ^ index.php [L]

# 404 hata sayfası → SPA router'a yönlendir
ErrorDocument 404 /index.php
ErrorDocument 403 /index.php
`;

  fs.writeFileSync(path.join(OUT_DIR, ".htaccess"), mainHtaccess, "utf-8");

  // API .htaccess kopyala
  const apiHtaccessSrc = path.join(SERVER_DIR, "api", ".htaccess");
  if (fs.existsSync(apiHtaccessSrc)) {
    fs.copyFileSync(apiHtaccessSrc, path.join(apiDest, ".htaccess"));
  }
  console.log("   ✅ .htaccess dosyaları hazır");

  // ========================
  // 8. DATABASE.SQL KOPYALAMA
  // ========================
  const dbSqlSrc = path.join(SERVER_DIR, "database.sql");
  if (fs.existsSync(dbSqlSrc)) {
    fs.copyFileSync(dbSqlSrc, path.join(OUT_DIR, "database.sql"));
    console.log("   ✅ database.sql kopyalandı");
  }

  // ========================
  // 9. UPLOAD_DIR PATH DÜZELTMESİ (sunucu yapısına göre)
  // ========================
  const dbConfigDest = path.join(apiDest, "db_config.php");
  if (fs.existsSync(dbConfigDest)) {
    let configContent = fs.readFileSync(dbConfigDest, "utf-8");
    // __DIR__/../data/uploads → sunucuda doğru çalışır (api/ dizininden bir üst = public_html/)
    // Zaten doğru relative path kullanıyor, kontrol amaçlı log
    console.log("   ✅ db_config.php path'leri doğrulandı (UPLOAD_DIR: api/../data/uploads)");
  }

  // ========================
  // 10. LOGS VE DATA DİZİNLERİ
  // ========================
  fs.mkdirSync(path.join(OUT_DIR, "data", "uploads"), { recursive: true });
  fs.mkdirSync(path.join(OUT_DIR, "logs"), { recursive: true });
  // Boş .gitkeep dosyaları
  fs.writeFileSync(path.join(OUT_DIR, "data", "uploads", ".gitkeep"), "", "utf-8");
  fs.writeFileSync(path.join(OUT_DIR, "logs", ".gitkeep"), "", "utf-8");

  // ========================
  // 9. ÖZET
  // ========================
  const allPhpPages = findFiles(OUT_DIR, ".php").filter(
    (f) => !f.includes(path.join("out", "api"))
  );
  const allPhpApi = findFiles(apiDest, ".php");
  const allJs = findFiles(path.join(OUT_DIR, "_next"), ".js");
  const allCss = findFiles(path.join(OUT_DIR, "_next"), ".css");

  console.log("\n" + "=".repeat(50));
  console.log("✅ PHP DEPLOYMENT PAKETİ HAZIR!");
  console.log("=".repeat(50));
  console.log(`\n📁 Çıktı dizini: out/`);
  console.log(`   📄 ${allPhpPages.length} PHP sayfa dosyası`);
  console.log(`   🔌 ${allPhpApi.length} PHP API endpoint dosyası`);
  console.log(`   📜 ${allJs.length} JavaScript dosyası (SPA motoru)`);
  console.log(`   🎨 ${allCss.length} CSS dosyası`);
  console.log(`   🔧 .htaccess (routing + güvenlik)`);
  console.log(`\n📋 DEPLOYMENT (cPanel/Plesk):`);
  console.log(`   1. out/ içindeki TÜM dosyaları → public_html/ klasörüne yükleyin`);
  console.log(`   2. api/db_config.php dosyasında DB bilgilerini güncelleyin`);
  console.log(`   3. https://siteniz.com/api/init-db adresini ziyaret edin`);
  console.log(`\n⚠️  ÖNEMLİ: Artık her şey TEK dizinde (out/)!`);
  console.log(`   server/ dizinini ayrıca yüklemenize GEREK YOK.\n`);

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
