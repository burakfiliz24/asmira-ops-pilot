<?php
// CORS ve ortak yardımcı fonksiyonlar
require_once __DIR__ . '/db_config.php';

function setCorsHeaders(): void {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: ' . CORS_ALLOWED_ORIGIN);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

function jsonResponse($data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonBody(): array {
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }
    return $decoded;
}

function getMethod(): string {
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * Zorunlu alanları kontrol et, eksik varsa 400 hata döndür
 */
function validateRequired(array $body, array $fields): void {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($body[$field]) || (is_string($body[$field]) && trim($body[$field]) === '')) {
            $missing[] = $field;
        }
    }
    if (!empty($missing)) {
        jsonResponse(['error' => 'Eksik alanlar: ' . implode(', ', $missing)], 400);
    }
}

/**
 * String değeri güvenli şekilde temizle (XSS koruması)
 */
function sanitize(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

/**
 * Dosya uzantısının izinli olup olmadığını kontrol et
 */
function isAllowedExtension(string $fileName): bool {
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    return in_array($ext, ALLOWED_EXTENSIONS);
}

/**
 * Hata yanıtı (APP_DEBUG moduna göre detay gösterir)
 */
function errorResponse(Exception $e, string $context = 'Sunucu hatası'): void {
    $response = ['error' => $context];
    if (defined('APP_DEBUG') && APP_DEBUG) {
        $response['details'] = $e->getMessage();
        $response['file'] = $e->getFile();
        $response['line'] = $e->getLine();
    }
    jsonResponse($response, 500);
}
