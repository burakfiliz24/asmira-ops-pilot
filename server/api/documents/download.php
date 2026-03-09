<?php
require_once __DIR__ . '/../config.php';

// Dosya yolunu URL'den al
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api/documents/download/';
$pos = strpos($requestUri, $basePath);

if ($pos === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Geçersiz istek']);
    exit;
}

$relativePath = substr($requestUri, $pos + strlen($basePath));
$relativePath = urldecode($relativePath);
$relativePath = str_replace('..', '', $relativePath); // Güvenlik

// DB'de dosya yolu "uploads/truck/xxx/doc.pdf" formatında
// UPLOAD_DIR zaten "data/uploads" dizinini gösteriyor, "uploads/" prefix'ini çıkar
$cleanPath = preg_replace('#^uploads/#', '', $relativePath);
$filePath = UPLOAD_DIR . '/' . $cleanPath;

if (!file_exists($filePath)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Dosya bulunamadı']);
    exit;
}

// MIME tipi belirle
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
];

$mime = $mimeTypes[$ext] ?? 'application/octet-stream';
$fileName = basename($filePath);

header("Content-Type: $mime");
header("Content-Disposition: inline; filename=\"$fileName\"");
header("Content-Length: " . filesize($filePath));
readfile($filePath);
exit;
