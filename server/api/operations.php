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
    if ($method === 'POST') { $b = getJsonBody(); $b['id'] = $b['id'] ?? ('op_' . time() . rand(100,999)); $b['offline'] = true; jsonResponse($b, 201); }
    jsonResponse(['success' => true, 'offline' => true]);
}


// Auto-migration: deliverer columns
try { $db->exec("ALTER TABLE operations ADD COLUMN deliverer_id VARCHAR(50) DEFAULT NULL"); } catch (Exception $e) {}
try { $db->exec("ALTER TABLE operations ADD COLUMN deliverer_name VARCHAR(255) DEFAULT NULL"); } catch (Exception $e) {}

// GET - Tüm operasyonları getir
if ($method === 'GET') {
    $stmt = $db->query("
        SELECT id, vessel_name as vesselName, vessel_type as vesselType, imo_number as imoNumber,
               quantity, unit, loading_place as loadingPlace, port, date, status,
               driver_name as driverName, driver_phone as driverPhone, agent_note as agentNote,
               deliverer_id as delivererId, deliverer_name as delivererName
        FROM operations ORDER BY date DESC
    ");
    jsonResponse($stmt->fetchAll());
}

// POST - Yeni operasyon oluştur
if ($method === 'POST') {
    $body = getJsonBody();
    validateRequired($body, ['vesselName', 'port', 'date']);
    $id = $body['id'] ?? ('op_' . time() . rand(100, 999));

    $stmt = $db->prepare("
        INSERT INTO operations (id, vessel_name, vessel_type, imo_number, quantity, unit, loading_place, port, date, status, driver_name, driver_phone, agent_note, deliverer_id, deliverer_name)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $id,
        $body['vesselName'],
        $body['vesselType'] ?? 'ship',
        $body['imoNumber'] ?? null,
        $body['quantity'] ?? 0,
        $body['unit'] ?? 'MT',
        $body['loadingPlace'] ?? null,
        $body['port'],
        $body['date'],
        $body['status'] ?? 'planned',
        $body['driverName'] ?? '',
        $body['driverPhone'] ?? '',
        $body['agentNote'] ?? '',
        $body['delivererId'] ?? null,
        $body['delivererName'] ?? null
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
        'driverName' => 'driver_name', 'driverPhone' => 'driver_phone', 'agentNote' => 'agent_note',
        'delivererId' => 'deliverer_id', 'delivererName' => 'deliverer_name'
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
    errorResponse($e, 'Operasyon işlemi hatası');
}
