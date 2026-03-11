<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
setCorsHeaders();

if (getMethod() !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {

$file = $_FILES['file'] ?? null;
$ownerId = $_POST['ownerId'] ?? '';
$ownerType = $_POST['ownerType'] ?? '';
$docType = $_POST['docType'] ?? '';
$expiryDate = $_POST['expiryDate'] ?? null;

if (!$file || !$ownerId || !$ownerType || !$docType) {
    jsonResponse(['error' => 'Eksik parametreler'], 400);
}

// Dosya uzantı kontrolü
if (!isAllowedExtension($file['name'])) {
    jsonResponse(['error' => 'İzin verilmeyen dosya türü. İzinli: ' . implode(', ', ALLOWED_EXTENSIONS)], 400);
}

// Dosya boyut kontrolü
if ($file['size'] > MAX_UPLOAD_SIZE) {
    jsonResponse(['error' => 'Dosya boyutu çok büyük. Maks: ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB'], 400);
}

// Klasör oluştur
$ownerDir = UPLOAD_DIR . "/$ownerType/$ownerId";
if (!is_dir($ownerDir)) {
    mkdir($ownerDir, 0755, true);
}

// Dosyayı kaydet
$ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'pdf';
$safeFileName = "$docType.$ext";
$filePath = "$ownerDir/$safeFileName";
$relativePath = "uploads/$ownerType/$ownerId/$safeFileName";

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    jsonResponse(['error' => 'Dosya kaydedilemedi'], 500);
}

// Veritabanını güncelle
$db = getDbSafe();
if (!$db) { jsonResponse(['success' => true, 'fileName' => $file['name'], 'filePath' => $relativePath, 'offline' => true]); }
$table = $ownerType === 'driver' ? 'driver_documents' : 'vehicle_documents';
$ownerCol = $ownerType === 'driver' ? 'driver_id' : 'owner_id';

if ($ownerType === 'driver') {
    $stmt = $db->prepare("SELECT id FROM $table WHERE $ownerCol = ? AND doc_type = ?");
    $stmt->execute([$ownerId, $docType]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $db->prepare("UPDATE $table SET file_name = ?, file_path = ?, expiry_date = ?, updated_at = NOW() WHERE $ownerCol = ? AND doc_type = ?");
        $stmt->execute([$file['name'], $relativePath, $expiryDate, $ownerId, $docType]);
    } else {
        $stmt = $db->prepare("INSERT INTO $table ($ownerCol, doc_type, label, file_name, file_path, expiry_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ownerId, $docType, $docType, $file['name'], $relativePath, $expiryDate]);
    }
} else {
    $stmt = $db->prepare("SELECT id FROM $table WHERE $ownerCol = ? AND owner_type = ? AND doc_type = ?");
    $stmt->execute([$ownerId, $ownerType, $docType]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $db->prepare("UPDATE $table SET file_name = ?, file_path = ?, expiry_date = ?, updated_at = NOW() WHERE $ownerCol = ? AND owner_type = ? AND doc_type = ?");
        $stmt->execute([$file['name'], $relativePath, $expiryDate, $ownerId, $ownerType, $docType]);
    } else {
        $stmt = $db->prepare("INSERT INTO $table ($ownerCol, owner_type, doc_type, label, file_name, file_path, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ownerId, $ownerType, $docType, $docType, $file['name'], $relativePath, $expiryDate]);
    }
}

jsonResponse(['success' => true, 'fileName' => $file['name'], 'filePath' => $relativePath]);

} catch (Exception $e) {
    errorResponse($e, 'Dosya yükleme hatası');
}
