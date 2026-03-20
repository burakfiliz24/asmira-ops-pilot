<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../middleware.php';
setCorsHeaders();
requireApiAuth(true); // Login auth gerektirmez

if (getMethod() !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    // Rate limiting kontrolü
    if (!checkLoginRateLimit()) {
        jsonResponse(['error' => 'Çok fazla giriş denemesi. 5 dakika sonra tekrar deneyin.'], 429);
    }
    recordLoginAttempt();

    $body = getJsonBody();
    validateRequired($body, ['username', 'password']);

    $username = sanitize($body['username']);
    $password = $body['password'];

    $db = getDbSafe();
    $user = null;

    if ($db) {
        $stmt = $db->prepare("SELECT id, username, password, name, role FROM users WHERE LOWER(username) = LOWER(?)");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && verifyPassword($password, $user['password'])) {
            if (PASSWORD_HASH_ENABLED && strpos($user['password'], '$2y$') !== 0) {
                $hashed = hashPassword($password);
                $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashed, $user['id']]);
            }
            unset($user['password']);
            jsonResponse($user);
        }
    }

    // Fallback kullanıcılar (DB yokken)
    if (!$db) {
        $fallback = [
            ['id'=>'u1','username'=>'admin','password'=>'admin','name'=>'Yönetici','role'=>'admin'],
            ['id'=>'u2','username'=>'asmira','password'=>'asmira','name'=>'Asmira Operatör','role'=>'admin'],
            ['id'=>'u3','username'=>'user','password'=>'user','name'=>'Kullanıcı','role'=>'user'],
        ];
        foreach ($fallback as $fu) {
            if (strtolower($fu['username']) === strtolower($username) && $fu['password'] === $password) {
                unset($fu['password']);
                jsonResponse($fu);
            }
        }
    }

    jsonResponse(['error' => 'Kullanıcı adı veya şifre hatalı'], 401);
} catch (Exception $e) {
    errorResponse($e, 'Giriş hatası');
}
