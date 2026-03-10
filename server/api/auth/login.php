<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
setCorsHeaders();

if (getMethod() !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    $body = getJsonBody();
    validateRequired($body, ['username', 'password']);

    $username = sanitize($body['username']);
    $password = $body['password'];

    $db = getDb();
    $stmt = $db->prepare("SELECT id, username, password, name, role FROM users WHERE LOWER(username) = LOWER(?)");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !verifyPassword($password, $user['password'])) {
        jsonResponse(['error' => 'Kullanıcı adı veya şifre hatalı'], 401);
    }

    // Eğer eski plain-text şifre ise hash'e yükselt
    if (PASSWORD_HASH_ENABLED && strpos($user['password'], '$2y$') !== 0) {
        $hashed = hashPassword($password);
        $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashed, $user['id']]);
    }

    // Şifreyi yanıtta gönderme
    unset($user['password']);
    jsonResponse($user);
} catch (Exception $e) {
    errorResponse($e, 'Giriş hatası');
}
