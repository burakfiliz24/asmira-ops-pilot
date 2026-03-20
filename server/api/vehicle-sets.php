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
    if ($method === 'GET') jsonResponse([]);
    if ($method === 'POST') { $b = getJsonBody(); jsonResponse(['id' => 'set_' . time() . rand(100,999), 'truckId' => $b['truckId'] ?? '', 'trailerId' => $b['trailerId'] ?? null, 'category' => $b['category'] ?? 'asmira', 'vehicleType' => $b['vehicleType'] ?? 'tir', 'offline' => true], 201); }
    jsonResponse(['success'=>true,'offline'=>true]);
}

// GET
if ($method === 'GET') {
    try {
        $stmt = $db->query("SELECT id, truck_id as truckId, trailer_id as trailerId, category, vehicle_type as vehicleType FROM vehicle_sets");
    } catch (Exception $e) {
        // Eski şema — vehicle_type kolonu henüz yok
        $stmt = $db->query("SELECT id, truck_id as truckId, trailer_id as trailerId, category FROM vehicle_sets");
    }
    jsonResponse($stmt->fetchAll());
}

// Otomatik migration — vehicle_type ekle + trailer_id nullable yap
function autoMigrate($db) {
    static $done = false;
    if ($done) return;
    $done = true;
    try { $db->exec("ALTER TABLE vehicle_sets ADD COLUMN vehicle_type VARCHAR(20) NOT NULL DEFAULT 'tir'"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE vehicle_sets DROP FOREIGN KEY fk_vsets_trailer"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE vehicle_sets MODIFY COLUMN trailer_id VARCHAR(50) DEFAULT NULL"); } catch (Exception $e) {}
    try { $db->exec("ALTER TABLE vehicle_sets ADD CONSTRAINT fk_vsets_trailer FOREIGN KEY (trailer_id) REFERENCES trailers(id) ON DELETE SET NULL"); } catch (Exception $e) {}
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    if (empty($body['truckId'])) jsonResponse(['error' => 'truckId gerekli'], 400);
    $id = $body['id'] ?? ('set_' . ($body['category'] ?? 'asmira') . '_' . time() . rand(100, 999));
    $vehicleType = $body['vehicleType'] ?? 'tir';
    $trailerId = (!empty($body['trailerId'])) ? $body['trailerId'] : null;
    $category = $body['category'] ?? 'asmira';

    // 1) Önce doğrudan dene
    try {
        $stmt = $db->prepare("INSERT INTO vehicle_sets (id, truck_id, trailer_id, category, vehicle_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id, $body['truckId'], $trailerId, $category, $vehicleType]);
        jsonResponse(['id' => $id], 201);
    } catch (Exception $e1) {}

    // 2) Başarısız — otomatik migration çalıştır ve tekrar dene
    autoMigrate($db);
    try {
        $stmt = $db->prepare("INSERT INTO vehicle_sets (id, truck_id, trailer_id, category, vehicle_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id, $body['truckId'], $trailerId, $category, $vehicleType]);
        jsonResponse(['id' => $id], 201);
    } catch (Exception $e2) {
        jsonResponse(['error' => 'INSERT başarısız: ' . $e2->getMessage()], 500);
    }
}

// PUT - Araç tipi güncelle
if ($method === 'PUT') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $fields = [];
    $values = [];
    if (array_key_exists('trailerId', $body)) { $fields[] = 'trailer_id = ?'; $values[] = (!empty($body['trailerId'])) ? $body['trailerId'] : null; }
    if (isset($body['vehicleType'])) {
        try {
            $fields[] = 'vehicle_type = ?'; $values[] = $body['vehicleType'];
        } catch (Exception $e) { /* kolon yok */ }
    }
    if (empty($fields)) jsonResponse(['error' => 'Güncellenecek alan yok'], 400);
    $values[] = $id;
    try {
        $db->prepare("UPDATE vehicle_sets SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
    } catch (Exception $e) {
        // vehicle_type kolonu yoksa sadece trailer_id güncelle
        if (isset($body['trailerId'])) {
            $db->prepare("UPDATE vehicle_sets SET trailer_id = ? WHERE id = ?")->execute([$body['trailerId'] ?? '', $id]);
        }
    }
    jsonResponse(['success' => true]);
}

// DELETE
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $db->prepare("DELETE FROM vehicle_sets WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    jsonResponse(['error' => 'Araç seti işlemi hatası: ' . $e->getMessage()], 500);
}
