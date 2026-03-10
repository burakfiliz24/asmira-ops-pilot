<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDb();

// GET
if ($method === 'GET') {
    $stmt = $db->query("SELECT id, title, description, icon, slug FROM petition_categories ORDER BY title");
    jsonResponse($stmt->fetchAll());
}

// POST
if ($method === 'POST') {
    $body = getJsonBody();
    validateRequired($body, ['title', 'slug']);
    $id = $body['id'] ?? ('cat_' . time() . rand(100, 999));

    $stmt = $db->prepare("INSERT INTO petition_categories (id, title, description, icon, slug) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $body['title'], $body['description'] ?? '', $body['icon'] ?? 'FileText', $body['slug']]);
    jsonResponse(['id' => $id], 201);
}

// PUT
if ($method === 'PUT') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $allowed = ['title', 'description', 'icon', 'slug'];
    $fields = [];
    $values = [];

    foreach ($body as $key => $val) {
        if ($key !== 'id' && in_array($key, $allowed)) {
            $fields[] = "$key = ?";
            $values[] = $val;
        }
    }

    if (empty($fields)) jsonResponse(['error' => 'Güncellenecek alan yok'], 400);
    $values[] = $id;

    $stmt = $db->prepare("UPDATE petition_categories SET " . implode(", ", $fields) . " WHERE id = ?");
    $stmt->execute($values);
    jsonResponse(['success' => true]);
}

// DELETE
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $db->prepare("DELETE FROM petition_templates WHERE category = (SELECT slug FROM petition_categories WHERE id = ?)")->execute([$id]);
    $db->prepare("DELETE FROM petition_categories WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    errorResponse($e, 'Dilekçe kategorisi hatası');
}
