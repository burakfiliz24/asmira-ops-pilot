<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDb();

// GET - Tüm operasyonları getir
if ($method === 'GET') {
    $stmt = $db->query("
        SELECT id, vessel_name as vesselName, vessel_type as vesselType, imo_number as imoNumber,
               quantity, unit, loading_place as loadingPlace, port, date, status,
               driver_name as driverName, driver_phone as driverPhone, agent_note as agentNote
        FROM operations ORDER BY date DESC
    ");
    jsonResponse($stmt->fetchAll());
}

// POST - Yeni operasyon oluştur
if ($method === 'POST') {
    $body = getJsonBody();
    $id = $body['id'] ?? ('op_' . time() . rand(100, 999));

    $stmt = $db->prepare("
        INSERT INTO operations (id, vessel_name, vessel_type, imo_number, quantity, unit, loading_place, port, date, status, driver_name, driver_phone, agent_note)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $id,
        $body['vesselName'],
        $body['vesselType'] ?? 'ship',
        $body['imoNumber'] ?? null,
        $body['quantity'],
        $body['unit'] ?? 'MT',
        $body['loadingPlace'] ?? null,
        $body['port'],
        $body['date'],
        $body['status'] ?? 'planned',
        $body['driverName'] ?? '',
        $body['driverPhone'] ?? '',
        $body['agentNote'] ?? ''
    ]);

    jsonResponse(['id' => $id] + $body, 201);
}

// PUT - Operasyon güncelle
if ($method === 'PUT') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $fieldMap = [
        'vesselName' => 'vessel_name', 'vesselType' => 'vessel_type', 'imoNumber' => 'imo_number',
        'quantity' => 'quantity', 'unit' => 'unit', 'loadingPlace' => 'loading_place',
        'port' => 'port', 'date' => 'date', 'status' => 'status',
        'driverName' => 'driver_name', 'driverPhone' => 'driver_phone', 'agentNote' => 'agent_note'
    ];

    $fields = [];
    $values = [];

    foreach ($body as $key => $val) {
        if ($key !== 'id' && isset($fieldMap[$key])) {
            $fields[] = $fieldMap[$key] . " = ?";
            $values[] = $val;
        }
    }

    if (empty($fields)) jsonResponse(['error' => 'Güncellenecek alan yok'], 400);

    $fields[] = "updated_at = NOW()";
    $values[] = $id;

    $stmt = $db->prepare("UPDATE operations SET " . implode(", ", $fields) . " WHERE id = ?");
    $stmt->execute($values);
    jsonResponse(['success' => true]);
}

// DELETE - Operasyon sil
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $stmt = $db->prepare("DELETE FROM operations WHERE id = ?");
    $stmt->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    jsonResponse(['error' => 'Sunucu hatası', 'details' => $e->getMessage()], 500);
}
