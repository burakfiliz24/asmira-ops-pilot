<?php
/**
 * Asmira OPS Pilot - PHP Sunucu Giriş Noktası
 * 
 * Bu dosya, PHP sunucusunda SPA (Single Page Application) routing'i sağlar.
 * API istekleri → /api/ altındaki PHP dosyalarına yönlendirilir
 * Diğer istekler → index.html (Next.js static export) serve edilir
 * 
 * .htaccess varsa bu dosya kullanılmaz. .htaccess yoksa (nginx gibi)
 * tüm istekler bu dosyaya yönlendirilmelidir.
 */

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');

// 1. API istekleri → PHP backend'e yönlendir
if (strpos($requestUri, '/api/') === 0) {
    $apiPath = substr($requestUri, 5); // '/api/' kısmını çıkar
    $phpFile = __DIR__ . '/api/' . $apiPath . '.php';
    
    // Alt dizinler için kontrol (auth/login, documents/upload vs.)
    if (!file_exists($phpFile)) {
        $phpFile = __DIR__ . '/api/' . $apiPath;
        if (is_dir($phpFile)) {
            $phpFile .= '/index.php';
        }
    }
    
    if (file_exists($phpFile)) {
        require $phpFile;
        exit;
    }
    
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'API endpoint bulunamadı: ' . $apiPath]);
    exit;
}

// 2. Statik dosya istekleri (CSS, JS, resim vs.)
$staticFile = __DIR__ . $requestUri;
if ($requestUri !== '/' && file_exists($staticFile) && is_file($staticFile)) {
    // MIME tipi belirle
    $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'html' => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'pdf'  => 'application/pdf',
    ];
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    
    readfile($staticFile);
    exit;
}

// 3. SPA fallback - tüm diğer istekleri index.html'e yönlendir
$indexFile = __DIR__ . '/index.html';
if (file_exists($indexFile)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($indexFile);
    exit;
}

// 4. index.html bulunamazsa hata
http_response_code(500);
echo 'Uygulama dosyaları bulunamadı. Lütfen deployment talimatlarını kontrol edin.';
