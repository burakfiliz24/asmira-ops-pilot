<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/middleware.php';
setCorsHeaders();
requireApiAuth();

$method = getMethod();

try {
$db = getDbSafe();
if (!$db) {
    jsonResponse(['error' => 'Veritabanı bağlantısı kurulamadı'], 503);
}

// Tabloyu oluştur (yoksa)
$db->exec("CREATE TABLE IF NOT EXISTS port_wiki_data (id INT PRIMARY KEY DEFAULT 1, data LONGTEXT, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");

// GET - Port wiki verilerini getir
if ($method === 'GET') {
    $stmt = $db->query("SELECT data, updated_at FROM port_wiki_data WHERE id = 1");
    $row = $stmt->fetch();
    if ($row && $row['data']) {
        header('Content-Type: application/json');
        echo $row['data'];
    } else {
        jsonResponse(['_uninitialized' => true]);
    }
    exit;
}

// PUT - Port wiki verilerini kaydet
if ($method === 'PUT') {
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        jsonResponse(['error' => 'Geçersiz veri formatı'], 400);
    }
    $json = json_encode($decoded, JSON_UNESCAPED_UNICODE);

    $stmt = $db->prepare("SELECT id FROM port_wiki_data WHERE id = 1");
    $stmt->execute();

    if ($stmt->fetch()) {
        $db->prepare("UPDATE port_wiki_data SET data = ? WHERE id = 1")->execute([$json]);
    } else {
        $db->prepare("INSERT INTO port_wiki_data (id, data) VALUES (1, ?)")->execute([$json]);
    }

    // Kaydedildiğini doğrula
    $verify = $db->query("SELECT LENGTH(data) as len FROM port_wiki_data WHERE id = 1")->fetch();
    jsonResponse(['success' => true, 'savedBytes' => (int)($verify['len'] ?? 0), 'portCount' => count($decoded)]);
}

} catch (Exception $e) {
    jsonResponse(['error' => 'Port wiki hatası: ' . $e->getMessage()], 500);
}
