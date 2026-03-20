<?php
/**
 * Asmira OPS Pilot - Veritabanı Yapılandırması (db_config.php)
 * 
 * MariaDB/MySQL PDO bağlantısı, SQL Injection koruması (Prepared Statements),
 * güvenlik ayarları ve ortam yapılandırması.
 * 
 * KURULUM:
 * 1. Bu dosyayı sunucuya yükleyin
 * 2. Aşağıdaki DB bilgilerini kendi sunucunuza göre güncelleyin
 * 3. /api/init-db adresini ziyaret ederek tabloları oluşturun
 */

// ========================
// VERİTABANI AYARLARI
// ========================
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'c0opsdb');
define('DB_USER', 'c0ops');
define('DB_PASS', 'M29i_VwcAAtkP');
define('DB_CHARSET', 'utf8mb4');

// ========================
// UYGULAMA AYARLARI
// ========================
define('APP_ENV', 'production');       // 'development' veya 'production'
define('APP_DEBUG', false);            // true = detaylı hata mesajları
define('APP_TIMEZONE', 'Europe/Istanbul');

// ========================
// UPLOAD AYARLARI
// ========================
define('UPLOAD_DIR', __DIR__ . '/../data/uploads');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'tif', 'heic', 'heif', 'svg', 'doc', 'docx', 'xls', 'xlsx']);

// ========================
// GÜVENLİK AYARLARI
// ========================
// CORS: Otomatik protokol algılama (HTTP/HTTPS)
$_corsProto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
define('CORS_ALLOWED_ORIGIN', $_corsProto . '://ops.asmiralogistics.com');
define('PASSWORD_HASH_ENABLED', true); // Şifre hash'leme aktif/pasif

// ========================
// ORTAM YAPILANDIRMASI
// ========================
date_default_timezone_set(APP_TIMEZONE);

if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/php-error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '12M');

// ========================
// PDO VERİTABANI BAĞLANTISI
// (Singleton pattern - tek bağlantı)
// ========================
function getDb(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        "mysql:host=%s;port=%d;dbname=%s;charset=%s",
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    // Bağlantı zaman aşımı (saniye) - pdo_mysql yüklüyse
    if (defined('PDO::MYSQL_ATTR_CONNECT_TIMEOUT')) {
        $options[PDO::MYSQL_ATTR_CONNECT_TIMEOUT] = 2;
    }

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // MariaDB/MySQL özel ayarlar
    $pdo->exec("SET NAMES " . DB_CHARSET);
    $pdo->exec("SET time_zone = '+03:00'");

    return $pdo;
}

// ========================
// ŞİFRE YARDIMCI FONKSİYONLARI
// ========================

/**
 * Şifreyi güvenli şekilde hash'le (bcrypt)
 */
function hashPassword(string $password): string {
    if (!PASSWORD_HASH_ENABLED) return $password;
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Şifreyi doğrula (hem hash hem plain-text desteği - geriye uyumluluk)
 */
function verifyPassword(string $password, string $hash): bool {
    if (!PASSWORD_HASH_ENABLED) return $password === $hash;
    
    // Eğer hash bcrypt formatında değilse, plain-text karşılaştır (eski kayıtlar)
    if (strpos($hash, '$2y$') !== 0 && strpos($hash, '$2a$') !== 0) {
        return $password === $hash;
    }
    return password_verify($password, $hash);
}
