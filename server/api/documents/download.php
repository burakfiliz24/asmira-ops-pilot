<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../middleware.php';
requireApiAuth();

// CORS başlıkları (Content-Type sonra dosya tipine göre değişecek)
header('Access-Control-Allow-Origin: ' . CORS_ALLOWED_ORIGIN);
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (getMethod() !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    // Dosya yolunu URL'den al
    $requestUri = $_SERVER['REQUEST_URI'];
    $basePath = '/api/documents/download/';
    $pos = strpos($requestUri, $basePath);

    if ($pos === false) {
        jsonResponse(['error' => 'Geçersiz istek'], 400);
    }

    $relativePath = substr($requestUri, $pos + strlen($basePath));
    $relativePath = urldecode($relativePath);
    // Güvenlik: dizin traversal saldırılarını engelle
    $relativePath = str_replace('..', '', $relativePath);
    $relativePath = preg_replace('#[/\\\\]+#', '/', $relativePath); // çift slash temizle

    // DB'de dosya yolu "uploads/truck/xxx/doc.pdf" formatında
    // UPLOAD_DIR zaten "data/uploads" dizinini gösteriyor, "uploads/" prefix'ini çıkar
    $cleanPath = preg_replace('#^uploads/#', '', $relativePath);
    $filePath = UPLOAD_DIR . '/' . $cleanPath;

    // Dosya UPLOAD_DIR dışına çıkamaz (güvenlik kontrolü)
    $realUploadDir = realpath(UPLOAD_DIR);
    $realFilePath = realpath($filePath);
    if ($realFilePath === false || ($realUploadDir !== false && strpos($realFilePath, $realUploadDir) !== 0)) {
        jsonResponse(['error' => 'Geçersiz dosya yolu'], 403);
    }

    if (!file_exists($filePath)) {
        jsonResponse(['error' => 'Dosya bulunamadı'], 404);
    }

    // MIME tipi belirle
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'pdf'  => 'application/pdf',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    $mime = $mimeTypes[$ext] ?? 'application/octet-stream';
    $fileName = basename($filePath);

    header("Content-Type: $mime");
    header("Content-Disposition: inline; filename=\"$fileName\"");
    header("Content-Length: " . filesize($filePath));
    header("X-Content-Type-Options: nosniff");
    readfile($filePath);
    exit;

} catch (Exception $e) {
    errorResponse($e, 'Dosya indirme hatası');
}
