<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/middleware.php';
setCorsHeaders();
requireApiAuth();

$method = getMethod();

try {
$db = getDbSafe();
if (!$db) { if ($method === 'GET') jsonResponse([]); jsonResponse(['success'=>true,'offline'=>true]); }

// GET
if ($method === 'GET') {
    $stmt = $db->query("SELECT id, name, city, region, critical_warning as criticalWarning FROM ports ORDER BY name");
    $ports = $stmt->fetchAll();
    
    foreach ($ports as &$p) {
        // Documents
        $docStmt = $db->prepare("SELECT document_name FROM port_documents WHERE port_id = ?");
        $docStmt->execute([$p['id']]);
        $p['documents'] = array_column($docStmt->fetchAll(), 'document_name');
        
        // Contacts
        $conStmt = $db->prepare("SELECT name, phone, role FROM port_contacts WHERE port_id = ?");
        $conStmt->execute([$p['id']]);
        $p['contacts'] = $conStmt->fetchAll();
        
        // Notes
        $noteStmt = $db->prepare("SELECT note FROM port_notes WHERE port_id = ?");
        $noteStmt->execute([$p['id']]);
        $p['notes'] = array_column($noteStmt->fetchAll(), 'note');
    }
    jsonResponse($ports);
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    validateRequired($body, ['name', 'city']);
    $id = $body['id'] ?? ('port_' . time() . rand(100,999));
    $stmt = $db->prepare("INSERT INTO ports (id, name, city, region, critical_warning) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $body['name'], $body['city'], $body['region'] ?? 'Ege', $body['criticalWarning'] ?? null]);
    jsonResponse(['id' => $id] + $body, 201);
}

// DELETE
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);
    $db->prepare("DELETE FROM ports WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}

} catch (Exception $e) {
    errorResponse($e, 'Port işlemi hatası');
}
