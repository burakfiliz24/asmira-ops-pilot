<?php
/**
 * API Güvenlik Middleware
 * Tüm API endpoint'lerinin başında require edilir.
 * - Session tabanlı kimlik doğrulama
 * - CORS domain kontrolü
 * - Rate limiting (login için)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * API isteğinde oturum kontrolü yapar.
 * Oturum yoksa 401 döner.
 * $exempt = true ise kontrol atlanır (login endpoint'i için)
 */
function requireApiAuth(bool $exempt = false): void {
    if ($exempt) return;
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Oturum gerekli. Lütfen giriş yapın.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Login brute-force koruması
 * Aynı IP'den 5 dakikada en fazla 10 deneme
 */
function checkLoginRateLimit(): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $lockFile = __DIR__ . '/../data/login_attempts.json';
    $maxAttempts = 10;
    $windowSeconds = 300; // 5 dakika

    $attempts = [];
    if (file_exists($lockFile)) {
        $data = json_decode(file_get_contents($lockFile), true);
        if (is_array($data)) $attempts = $data;
    }

    $now = time();
    // Eski kayıtları temizle
    if (isset($attempts[$ip])) {
        $attempts[$ip] = array_filter($attempts[$ip], fn($t) => ($now - $t) < $windowSeconds);
    }

    $count = count($attempts[$ip] ?? []);
    if ($count >= $maxAttempts) {
        return false; // Rate limit aşıldı
    }

    return true;
}

/**
 * Login denemesini kaydet
 */
function recordLoginAttempt(): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $lockFile = __DIR__ . '/../data/login_attempts.json';

    $attempts = [];
    if (file_exists($lockFile)) {
        $data = json_decode(file_get_contents($lockFile), true);
        if (is_array($data)) $attempts = $data;
    }

    $attempts[$ip][] = time();

    // Eski IP kayıtlarını temizle (1 saatten eski)
    $now = time();
    foreach ($attempts as $aip => &$times) {
        $times = array_values(array_filter($times, fn($t) => ($now - $t) < 3600));
        if (empty($times)) unset($attempts[$aip]);
    }

    @file_put_contents($lockFile, json_encode($attempts), LOCK_EX);
}

/**
 * Başarılı login sonrası denemeleri sıfırla
 */
function clearLoginAttempts(): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $lockFile = __DIR__ . '/../data/login_attempts.json';

    $attempts = [];
    if (file_exists($lockFile)) {
        $data = json_decode(file_get_contents($lockFile), true);
        if (is_array($data)) $attempts = $data;
    }

    unset($attempts[$ip]);
    @file_put_contents($lockFile, json_encode($attempts), LOCK_EX);
}
