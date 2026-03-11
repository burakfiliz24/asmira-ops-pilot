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
