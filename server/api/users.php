<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
setCorsHeaders();

$method = getMethod();

try {
$db = getDb();

// GET - Tüm kullanıcıları getir
if ($method === 'GET') {
    $stmt = $db->query("SELECT id, username, password, name, role FROM users ORDER BY created_at");
    jsonResponse($stmt->fetchAll());
}

// POST - Yeni kullanıcı oluştur
if ($method === 'POST') {
    $body = getJsonBody();
    validateRequired($body, ['username', 'password', 'name']);
    $id = 'user_' . time() . rand(100, 999);

    $stmt = $db->prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(?)");
    $stmt->execute([sanitize($body['username'])]);
    if ($stmt->fetch()) {
        jsonResponse(['error' => 'Bu kullanıcı adı zaten mevcut'], 409);
    }

    $hashedPassword = hashPassword($body['password']);
    $stmt = $db->prepare("INSERT INTO users (id, username, password, name, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, sanitize($body['username']), $hashedPassword, sanitize($body['name']), $body['role'] ?? 'user']);
    jsonResponse(['id' => $id, 'username' => $body['username'], 'name' => $body['name'], 'role' => $body['role'] ?? 'user'], 201);
}

// PUT - Kullanıcı güncelle
if ($method === 'PUT') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    $allowed = ['username', 'password', 'name', 'role'];
    $fields = [];
    $values = [];

    foreach ($body as $key => $val) {
        if ($key !== 'id' && in_array($key, $allowed)) {
            $fields[] = "$key = ?";
            // Şifre güncelleniyorsa hash'le
            $values[] = ($key === 'password') ? hashPassword($val) : $val;
        }
    }

    if (empty($fields)) jsonResponse(['error' => 'Güncellenecek alan yok'], 400);
    $values[] = $id;

    $stmt = $db->prepare("UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?");
    $stmt->execute($values);
    jsonResponse(['success' => true]);
}

// DELETE - Kullanıcı sil
if ($method === 'DELETE') {
    $body = getJsonBody();
    $id = $body['id'] ?? '';
    if (!$id) jsonResponse(['error' => 'id gerekli'], 400);

    // Son admin silinemez
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $adminCount = $stmt->fetch()['count'];

    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user && $user['role'] === 'admin' && $adminCount <= 1) {
        jsonResponse(['error' => 'Son yönetici silinemez'], 400);
    }

    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    jsonResponse(['success' => true]);
}
} catch (Exception $e) {
    errorResponse($e, 'Kullanıcı işlemi hatası');
}
