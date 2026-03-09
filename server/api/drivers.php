<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDb();

// GET
if ($method === 'GET') {
    $drivers = $db->query("SELECT id, name, tc_no as tcNo, phone FROM drivers ORDER BY created_at")->fetchAll();
    $docStmt = $db->prepare("SELECT doc_type as type, label, file_name as fileName, file_path as filePath, expiry_date as expiryDate FROM driver_documents WHERE driver_id = ?");
    foreach ($drivers as &$d) {
        $docStmt->execute([$d['id']]);
        $d['documents'] = $docStmt->fetchAll();
    }
    jsonResponse($drivers);
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    $id = $body['id'] ?? ('driver_' . time() . rand(100, 999));

    $stmt = $db->prepare("INSERT INTO drivers (id, name, tc_no, phone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $body['name'], $body['tcNo'], $body['phone']]);

    if (!empty($body['documents']) && is_array($body['documents'])) {
        $docStmt = $db->prepare("INSERT INTO driver_documents (driver_id, doc_type, label) VALUES (?, ?, ?)");
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

    $stmt = $db->prepare("UPDATE drivers SET name = ?, tc_no = ?, phone = ? WHERE id = ?");
    $stmt->execute([$body['name'], $body['tcNo'], $body['phone'], $id]);
    jsonResponse(['success' => true]);
}

// DELETE
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $db->prepare("DELETE FROM driver_documents WHERE driver_id = ?")->execute([$id]);
    $db->prepare("DELETE FROM drivers WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    jsonResponse(['error' => 'Sunucu hatası', 'details' => $e->getMessage()], 500);
}
