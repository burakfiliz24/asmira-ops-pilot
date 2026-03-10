<?php
/**
 * PHP Built-in Server Router
 * .htaccess desteği olmadığı için bu dosya routing'i sağlar.
 * Kullanım: php -S localhost:8080 -t out/ server/router.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$docRoot = $_SERVER['DOCUMENT_ROOT'];

// 1. Statik dosya varsa doğrudan serve et (JS, CSS, resim, font vs.)
$filePath = $docRoot . $uri;
if ($uri !== '/' && is_file($filePath)) {
    // PHP dosyası ise çalıştır
    if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
        return false; // PHP built-in server'a bırak
    }
    return false; // Statik dosyayı serve et
}

// 2. API istekleri → PHP backend
if (strpos($uri, '/api/') === 0) {
    $apiPath = substr($uri, 5); // '/api/' kısmını çıkar
    
    // Uzantısız endpoint'ler → .php ekle
    $phpFile = $docRoot . '/api/' . $apiPath;
    if (!is_file($phpFile) && is_file($phpFile . '.php')) {
        $_SERVER['SCRIPT_FILENAME'] = $phpFile . '.php';
        include $phpFile . '.php';
        return true;
    }
    
    // Doğrudan PHP dosyası
    if (is_file($phpFile)) {
        if (pathinfo($phpFile, PATHINFO_EXTENSION) === 'php') {
            $_SERVER['SCRIPT_FILENAME'] = $phpFile;
            include $phpFile;
            return true;
        }
        return false;
    }
    
    // Alt dizin kontrolü (auth/login → auth/login.php)
    $parts = explode('/', trim($apiPath, '/'));
    if (count($parts) >= 2) {
        $subFile = $docRoot . '/api/' . implode('/', $parts) . '.php';
        if (is_file($subFile)) {
            $_SERVER['SCRIPT_FILENAME'] = $subFile;
            include $subFile;
            return true;
        }
    }
    
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'API endpoint bulunamadı']);
    return true;
}

// 3. Sayfa istekleri → dizin/index.php
$dirIndexPhp = $docRoot . $uri . '/index.php';
if (is_file($dirIndexPhp)) {
    $_SERVER['SCRIPT_FILENAME'] = $dirIndexPhp;
    include $dirIndexPhp;
    return true;
}

// 4. .php uzantısız erişim → .php dosyasına yönlendir
$phpEquiv = $docRoot . $uri . '.php';
if (is_file($phpEquiv)) {
    $_SERVER['SCRIPT_FILENAME'] = $phpEquiv;
    include $phpEquiv;
    return true;
}

// 5. Fallback → index.php (SPA routing)
$indexPhp = $docRoot . '/index.php';
if (is_file($indexPhp)) {
    $_SERVER['SCRIPT_FILENAME'] = $indexPhp;
    include $indexPhp;
    return true;
}

http_response_code(404);
echo 'Sayfa bulunamadı';
return true;
