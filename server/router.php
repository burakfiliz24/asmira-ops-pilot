<?php
/**
 * PHP Built-in Server Router
 * Kullanım: php -S 127.0.0.1:3000 -t server/ server/router.php
 * 
 * Tüm istekleri index.php front controller'a yönlendirir.
 * Statik dosyalar (CSS, JS, resim) doğrudan serve edilir.
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$docRoot = $_SERVER['DOCUMENT_ROOT'];

// Statik dosya varsa doğrudan serve et
$filePath = $docRoot . $uri;
if ($uri !== '/' && is_file($filePath)) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    // PHP dosyalarına doğrudan erişimi engelle (index.php hariç)
    if ($ext === 'php' && basename($filePath) !== 'index.php') {
        // includes/ ve pages/ dizinlerine doğrudan erişimi engelle
        if (str_contains($uri, '/includes/') || str_contains($uri, '/pages/')) {
            http_response_code(403);
            echo 'Erişim engellendi';
            return true;
        }
    }
    return false; // Built-in server'a bırak
}

// Diğer tüm istekleri front controller'a yönlendir
$indexPhp = $docRoot . '/index.php';
if (is_file($indexPhp)) {
    include $indexPhp;
    return true;
}

http_response_code(404);
echo 'index.php bulunamadı';
return true;
