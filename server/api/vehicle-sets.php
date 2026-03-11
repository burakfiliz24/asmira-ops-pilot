<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDbSafe();
if (!$db) {
    if ($method === 'GET') jsonResponse([]);
    if ($method === 'POST') { $b = getJsonBody(); jsonResponse(['id' => 'set_' . time() . rand(100,999), 'truckId' => $b['truckId'] ?? '', 'trailerId' => $b['trailerId'] ?? '', 'category' => $b['category'] ?? 'asmira', 'offline' => true], 201); }
    jsonResponse(['success'=>true,'offline'=>true]);
}

// GET
if ($method === 'GET') {
    $stmt = $db->query("SELECT id, truck_id as truckId, trailer_id as trailerId, category FROM vehicle_sets");
    jsonResponse($stmt->fetchAll());
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    validateRequired($body, ['truckId', 'trailerId']);
    $id = $body['id'] ?? ('set_' . ($body['category'] ?? 'asmira') . '_' . time() . rand(100, 999));

    $stmt = $db->prepare("INSERT INTO vehicle_sets (id, truck_id, trailer_id, category) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $body['truckId'], $body['trailerId'], $body['category'] ?? 'asmira']);
    jsonResponse(['id' => $id], 201);
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
    errorResponse($e, 'Araç seti işlemi hatası');
}
