<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDb();

// GET
if ($method === 'GET') {
    $trucks = $db->query("SELECT id, plate, category FROM trucks ORDER BY created_at")->fetchAll();
    $docStmt = $db->prepare("SELECT doc_type as type, label, file_name as fileName, file_path as filePath, expiry_date as expiryDate FROM vehicle_documents WHERE owner_id = ? AND owner_type = 'truck'");
    foreach ($trucks as &$t) {
        $docStmt->execute([$t['id']]);
        $t['documents'] = $docStmt->fetchAll();
    }
    jsonResponse($trucks);
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    validateRequired($body, ['plate']);
    $id = $body['id'] ?? ('truck_' . time() . rand(100, 999));

    $stmt = $db->prepare("INSERT INTO trucks (id, plate, category) VALUES (?, ?, ?)");
    $stmt->execute([$id, $body['plate'], $body['category'] ?? 'asmira']);

    if (!empty($body['documents']) && is_array($body['documents'])) {
        $docStmt = $db->prepare("INSERT INTO vehicle_documents (owner_id, owner_type, doc_type, label) VALUES (?, 'truck', ?, ?)");
        foreach ($body['documents'] as $doc) {
            $docStmt->execute([$id, $doc['type'], $doc['label']]);
        }
    }
    jsonResponse(['id' => $id], 201);
}

// PUT
if ($method === 'PUT') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $stmt = $db->prepare("UPDATE trucks SET plate = ? WHERE id = ?");
    $stmt->execute([$body['plate'], $id]);
    jsonResponse(['success' => true]);
}

// DELETE
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $db->prepare("DELETE FROM vehicle_documents WHERE owner_id = ? AND owner_type = 'truck'")->execute([$id]);
    $db->prepare("DELETE FROM trucks WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    errorResponse($e, 'Çekici işlemi hatası');
}
