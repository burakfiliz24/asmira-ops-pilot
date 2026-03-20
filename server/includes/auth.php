<?php
/**
 * Asmira Ops - Kimlik Doğrulama (Session tabanlı)
 * Her korumalı sayfanın başında include edilir.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kullanıcının oturum açıp açmadığını kontrol eder.
 * Açmamışsa login sayfasına yönlendirir.
 */
function requireAuth(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}

/**
 * Kullanıcının admin olup olmadığını kontrol eder.
 */
function isAdmin(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Kullanıcının yönetici olup olmadığını kontrol eder.
 */
function isManager(): bool
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manager';
}

/**
 * Kullanıcının yönetici veya admin olup olmadığını kontrol eder.
 * Ayarlar sayfasına erişim için kullanılır.
 */
function isManagerOrAdmin(): bool
{
    return isAdmin() || isManager();
}

/**
 * Oturum açmış kullanıcı bilgisini döndürür.
 */
function getCurrentUser(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['user_username'] ?? '',
        'name'     => $_SESSION['user_name'] ?? '',
        'role'     => $_SESSION['user_role'] ?? 'user',
    ];
}

/**
 * Oturum açma
 */
function loginUser(array $user): void
{
    $_SESSION['user_id']       = $user['id'];
    $_SESSION['user_username'] = $user['username'];
    $_SESSION['user_name']     = $user['name'];
    $_SESSION['user_role']     = $user['role'];
}

/**
 * Oturumu kapat
 */
function logoutUser(): void
{
    session_destroy();
}
