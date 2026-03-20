<?php
/**
 * Asmira OPS - PHP Front Controller
 * 
 * Tüm istekleri yönlendirir:
 * - /api/* → PHP API endpoint'leri
 * - /assets/* → Statik dosyalar
 * - /login, /logout → Kimlik doğrulama
 * - Diğer → PHP sayfa dosyaları
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (session_status() === PHP_SESSION_NONE) {
    // Session güvenlik ayarları
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    ini_set('session.cookie_httponly', '1');            // JS ile cookie erişimini engelle
    ini_set('session.cookie_secure', $isHttps ? '1' : '0'); // HTTPS varsa secure cookie
    ini_set('session.cookie_samesite', 'Lax');         // CSRF koruması
    ini_set('session.use_strict_mode', '1');           // Geçersiz session ID'leri reddet
    ini_set('session.use_only_cookies', '1');          // URL'de session ID kullanma
    session_start();
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/') ?: '/';

// 1. API istekleri → PHP backend'e yönlendir
if (str_starts_with($requestUri, '/api/')) {
    $apiPath = substr($requestUri, 5);
    $phpFile = __DIR__ . '/api/' . $apiPath . '.php';
    
    if (!file_exists($phpFile)) {
        $phpFile = __DIR__ . '/api/' . $apiPath;
        if (is_dir($phpFile)) {
            $phpFile .= '/index.php';
        }
    }
    
    // Alt yol segmentleri olan istekler için (ör: /api/documents/download/uploads/...)
    // Sağdan segmentleri kaldırarak PHP dosyasını bul
    if (!file_exists($phpFile)) {
        $segments = explode('/', $apiPath);
        for ($i = count($segments); $i >= 1; $i--) {
            $tryPath = implode('/', array_slice($segments, 0, $i));
            $tryFile = __DIR__ . '/api/' . $tryPath . '.php';
            if (file_exists($tryFile)) {
                $phpFile = $tryFile;
                break;
            }
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
    $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'html' => 'text/html', 'css' => 'text/css', 'js' => 'application/javascript',
        'json' => 'application/json', 'png' => 'image/png', 'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon', 'woff' => 'font/woff', 'woff2' => 'font/woff2',
        'ttf' => 'font/ttf', 'pdf' => 'application/pdf',
    ];
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    readfile($staticFile);
    exit;
}

// 3. Logout
if ($requestUri === '/logout') {
    session_destroy();
    header('Location: /login');
    exit;
}

// 4. Sayfa routing
$routes = [
    '/'                              => 'login',
    '/login'                         => 'login',
    '/dashboard'                     => 'dashboard',
    '/vehicle-documents'             => 'vehicle-documents',
    '/vehicle-documents/asmira'      => 'vehicle-documents-asmira',
    '/vehicle-documents/suppliers'   => 'vehicle-documents-suppliers',
    '/vehicle-documents/new'         => 'vehicle-documents-new',
    '/driver-documents'              => 'driver-documents',
    '/driver-documents/asmira'       => 'driver-documents',
    '/driver-documents/suppliers'    => 'driver-documents-suppliers',
    '/delivery-documents'             => 'delivery-documents',
    '/document-package'              => 'document-package',
    '/petitions'                     => 'petitions',
    '/reports'                       => 'reports',
    '/reports/operations'            => 'reports-operations',
    '/reports/document-tracking'     => 'reports-document-tracking',
    '/port-wiki'                     => 'port-wiki',
    '/port-wiki/ports'               => 'port-wiki-ports',
    '/settings'                      => 'settings',
];

// Tam eşleşme
if (isset($routes[$requestUri])) {
    $GLOBALS['currentPage'] = $requestUri;
    require __DIR__ . '/pages/' . $routes[$requestUri] . '.php';
    exit;
}

// Dinamik route: /vehicle-documents/{id}
if (preg_match('#^/vehicle-documents/([a-zA-Z0-9_-]+)$#', $requestUri, $m)) {
    if (!in_array($m[1], ['asmira', 'suppliers', 'new'])) {
        $_GET['id'] = $m[1];
        $GLOBALS['currentPage'] = '/vehicle-documents';
        require __DIR__ . '/pages/vehicle-documents-detail.php';
        exit;
    }
}

// Dinamik route: /petitions/{slug}
if (preg_match('#^/petitions/([a-zA-Z0-9_-]+)$#', $requestUri, $m)) {
    $_GET['slug'] = $m[1];
    $GLOBALS['currentPage'] = '/petitions';
    require __DIR__ . '/pages/petitions-category.php';
    exit;
}

// 5. 404
http_response_code(404);
require_once __DIR__ . '/includes/auth.php';
if (isset($_SESSION['user_id'])) {
    $GLOBALS['currentPage'] = '';
    $pageTitle = '404 - Sayfa Bulunamadı';
    require __DIR__ . '/includes/header.php';
    echo '<div class="flex flex-col items-center justify-center py-24"><h1 class="text-4xl font-bold text-white/80 mb-4">404</h1><p class="text-white/50 mb-8">Sayfa bulunamadı</p><a href="/dashboard" class="btn btn-primary">Dashboard\'a Dön</a></div>';
    require __DIR__ . '/includes/footer.php';
} else {
    header('Location: /login');
}
