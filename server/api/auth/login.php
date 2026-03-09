<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
setCorsHeaders();

if (getMethod() !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    $body = getJsonBody();
    $username = $body['username'] ?? '';
    $password = $body['password'] ?? '';

    if (!$username || !$password) {
        jsonResponse(['error' => 'Kullanıcı adı ve şifre gerekli'], 400);
    }

    $db = getDb();
    $stmt = $db->prepare("SELECT id, username, name, role FROM users WHERE LOWER(username) = LOWER(?) AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(['error' => 'Kullanıcı adı veya şifre hatalı'], 401);
    }

    jsonResponse($user);
} catch (Exception $e) {
    jsonResponse(['error' => 'Sunucu hatası', 'details' => $e->getMessage()], 500);
}
