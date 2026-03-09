<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDb();

// GET
if ($method === 'GET') {
    $stmt = $db->query("SELECT id, short_name as shortName, name, default_text as defaultText, category, is_default as isDefault, created_at as createdAt FROM petition_templates ORDER BY created_at");
    jsonResponse($stmt->fetchAll());
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    $id = $body['id'] ?? ('tpl_' . time() . rand(100, 999));

    $stmt = $db->prepare("INSERT INTO petition_templates (id, short_name, name, default_text, category, is_default, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $id,
        $body['shortName'],
        $body['name'],
        $body['defaultText'] ?? '',
        $body['category'],
        $body['isDefault'] ?? 0,
        $body['createdAt'] ?? time()
    ]);
    jsonResponse(['id' => $id], 201);
}

// PUT
if ($method === 'PUT') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $fieldMap = [
        'shortName' => 'short_name', 'name' => 'name',
        'defaultText' => 'default_text', 'category' => 'category',
        'isDefault' => 'is_default'
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
    $values[] = $id;

    $stmt = $db->prepare("UPDATE petition_templates SET " . implode(", ", $fields) . " WHERE id = ?");
    $stmt->execute($values);
    jsonResponse(['success' => true]);
}

// DELETE
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $db->prepare("DELETE FROM petition_templates WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    jsonResponse(['error' => 'Sunucu hatası', 'details' => $e->getMessage()], 500);
}
