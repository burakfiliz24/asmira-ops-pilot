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
    if ($method === 'GET') jsonResponse([
        ['id'=>'cat_taahhutname','title'=>'Taahhütnameler','description'=>'Taşıma ve güvenlik taahhütname şablonları','icon'=>'FileCheck','slug'=>'taahhutnameler'],
        ['id'=>'cat_gumruk','title'=>'Gümrük Dilekçeleri','description'=>'Gümrük işlemleri için dilekçe şablonları','icon'=>'Shield','slug'=>'gumruk-dilekceleri'],
        ['id'=>'cat_ek1','title'=>'EK-1 Belgeleri','description'=>'EK-1 formu ve ek belge şablonları','icon'=>'ClipboardList','slug'=>'ek-1-belgeleri'],
    ]);
    if ($method === 'POST') { $b = getJsonBody(); $b['id'] = 'cat_' . time() . rand(100,999); $b['slug'] = $b['slug'] ?? strtolower(preg_replace('/[^a-z0-9]+/','_',str_replace(['ı','ğ','ü','ş','ö','ç','İ','Ğ','Ü','Ş','Ö','Ç'],['i','g','u','s','o','c','i','g','u','s','o','c'],$b['title'] ?? ''))); jsonResponse($b, 201); }
    jsonResponse(['success'=>true,'offline'=>true]);
}

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
