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
    if ($method === 'POST') { $b = getJsonBody(); jsonResponse(['id' => 'dlv_' . time() . rand(100,999), 'name' => $b['name'] ?? '', 'tcNo' => $b['tcNo'] ?? '', 'documents' => [], 'offline' => true], 201); }
    jsonResponse(['success'=>true,'offline'=>true]);
}

// Auto-create table if not exists
$db->exec("CREATE TABLE IF NOT EXISTS deliverers (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    tc_no VARCHAR(20),
    phone VARCHAR(30),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
$db->exec("CREATE TABLE IF NOT EXISTS deliverer_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deliverer_id VARCHAR(50) NOT NULL,
    doc_type VARCHAR(100) NOT NULL,
    label VARCHAR(255),
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    expiry_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deliverer_id) REFERENCES deliverers(id)
)");

// GET
if ($method === 'GET') {
    $deliverers = $db->query("SELECT id, name, tc_no as tcNo, phone FROM deliverers ORDER BY created_at")->fetchAll();
    $docStmt = $db->prepare("SELECT doc_type as type, label, file_name as fileName, file_path as filePath, expiry_date as expiryDate FROM deliverer_documents WHERE deliverer_id = ?");
    foreach ($deliverers as &$d) {
        $docStmt->execute([$d['id']]);
        $d['documents'] = $docStmt->fetchAll();
    }
    jsonResponse($deliverers);
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    validateRequired($body, ['name']);
    $id = $body['id'] ?? ('dlv_' . time() . rand(100, 999));

    $stmt = $db->prepare("INSERT INTO deliverers (id, name, tc_no, phone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $body['name'], $body['tcNo'] ?? '', $body['phone'] ?? '']);

    if (!empty($body['documents']) && is_array($body['documents'])) {
        $docStmt = $db->prepare("INSERT INTO deliverer_documents (deliverer_id, doc_type, label) VALUES (?, ?, ?)");
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

    $stmt = $db->prepare("UPDATE deliverers SET name = ?, tc_no = ?, phone = ? WHERE id = ?");
    $stmt->execute([$body['name'], $body['tcNo'] ?? '', $body['phone'] ?? '', $id]);
    jsonResponse(['success' => true]);
}

// DELETE
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $db->prepare("DELETE FROM deliverer_documents WHERE deliverer_id = ?")->execute([$id]);
    $db->prepare("DELETE FROM deliverers WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    errorResponse($e, 'Teslimatçı işlemi hatası');
}
