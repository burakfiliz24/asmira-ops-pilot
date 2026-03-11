<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDbSafe();
if (!$db) { if ($method === 'GET') jsonResponse([]); jsonResponse(['success'=>true,'offline'=>true]); }

// GET - Tedarikçi firmalarını getir
if ($method === 'GET') {
    $stmt = $db->query("SELECT data FROM supplier_data WHERE id = 1");
    $row = $stmt->fetch();
    if ($row) {
        header('Content-Type: application/json');
        echo $row['data'];
    } else {
        jsonResponse([]);
    }
    exit;
}

// PUT - Tedarikçi firmalarını kaydet
if ($method === 'PUT') {
    $body = getJsonBody();
    $json = json_encode($body, JSON_UNESCAPED_UNICODE);

    $stmt = $db->prepare("SELECT id FROM supplier_data WHERE id = 1");
    $stmt->execute();

    if ($stmt->fetch()) {
        $db->prepare("UPDATE supplier_data SET data = ? WHERE id = 1")->execute([$json]);
    } else {
        $db->prepare("INSERT INTO supplier_data (id, data) VALUES (1, ?)")->execute([$json]);
    }

    jsonResponse(['success' => true]);
}

} catch (Exception $e) {
    errorResponse($e, 'Tedarikçi firma hatası');
}
