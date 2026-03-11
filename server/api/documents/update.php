<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
setCorsHeaders();

$method = getMethod();
$body = getJsonBody();

try {
$db = getDbSafe();
if (!$db) { jsonResponse(['success' => true, 'offline' => true]); }

$ownerId = $body['ownerId'] ?? '';
$ownerType = $body['ownerType'] ?? '';
$docType = $body['docType'] ?? '';

if (!$ownerId || !$ownerType || !$docType) {
    jsonResponse(['error' => 'Eksik parametreler'], 400);
}

// PUT - Evrak tarih güncelleme
if ($method === 'PUT') {
    $expiryDate = $body['expiryDate'] ?? null;

    if ($ownerType === 'driver') {
        $stmt = $db->prepare("UPDATE driver_documents SET expiry_date = ?, updated_at = NOW() WHERE driver_id = ? AND doc_type = ?");
        $stmt->execute([$expiryDate, $ownerId, $docType]);
    } else {
        $stmt = $db->prepare("UPDATE vehicle_documents SET expiry_date = ?, updated_at = NOW() WHERE owner_id = ? AND owner_type = ? AND doc_type = ?");
        $stmt->execute([$expiryDate, $ownerId, $ownerType, $docType]);
    }
    jsonResponse(['success' => true]);
}

// DELETE - Evrak silme (dosya + kayıt sıfırlama)
if ($method === 'DELETE') {
    // Önce DB'den gerçek dosya yolunu bul
    if ($ownerType === 'driver') {
        $stmt = $db->prepare("SELECT file_path FROM driver_documents WHERE driver_id = ? AND doc_type = ?");
        $stmt->execute([$ownerId, $docType]);
    } else {
        $stmt = $db->prepare("SELECT file_path FROM vehicle_documents WHERE owner_id = ? AND owner_type = ? AND doc_type = ?");
        $stmt->execute([$ownerId, $ownerType, $docType]);
    }
    $row = $stmt->fetch();

    // Dosyayı diskten sil (file_path: "uploads/truck/xxx/doc.ext")
    if ($row && $row['file_path']) {
        $cleanPath = preg_replace('#^uploads/#', '', $row['file_path']);
        $fullPath = UPLOAD_DIR . '/' . $cleanPath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    // DB kaydını sıfırla
    if ($ownerType === 'driver') {
        $stmt = $db->prepare("UPDATE driver_documents SET file_name = NULL, file_path = NULL, expiry_date = NULL, updated_at = NOW() WHERE driver_id = ? AND doc_type = ?");
        $stmt->execute([$ownerId, $docType]);
    } else {
        $stmt = $db->prepare("UPDATE vehicle_documents SET file_name = NULL, file_path = NULL, expiry_date = NULL, updated_at = NOW() WHERE owner_id = ? AND owner_type = ? AND doc_type = ?");
        $stmt->execute([$ownerId, $ownerType, $docType]);
    }
    jsonResponse(['success' => true]);
}

} catch (Exception $e) {
    errorResponse($e, 'Evrak güncelleme hatası');
}
