<?php
/**
 * Veritabanı bağlantı dosyası
 * Tüm bağlantı ve güvenlik ayarları db_config.php'de tanımlıdır.
 * getDb() fonksiyonu db_config.php içinde mevcuttur.
 */
require_once __DIR__ . '/db_config.php';

/**
 * DB bağlantısını güvenli şekilde dener.
 * Bağlantı kurulamazsa null döner (lokal test desteği).
 */
function getDbSafe(): ?PDO {
    try {
        return getDb();
    } catch (\Throwable $e) {
        return null;
    }
}
