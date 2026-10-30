<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['activity_log'])) {
    $_SESSION['activity_log'] = array();
}

function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = array(
        'id_user' => (int)$user['id_user'],
        'nama' => $user['nama'],
        'username' => $user['username'],
        'role' => $user['role']
    );
}

function logoutUser(): void
{
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: index.php?page=login');
        exit;
    }
}

function requireRole(array $roles): void
{
    requireLogin();
    $user = currentUser();
    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        die('Akses ditolak.');
    }
}

function addActivity(string $text): void
{
    if (!isset($_SESSION['activity_log'])) {
        $_SESSION['activity_log'] = array();
    }
    array_unshift($_SESSION['activity_log'], array(
        'time' => date('Y-m-d H:i:s'),
        'user' => $_SESSION['user']['nama'] ?? 'Guest',
        'text' => $text
    ));
    $_SESSION['activity_log'] = array_slice($_SESSION['activity_log'], 0, 100);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = array('type' => $type, 'message' => $message);
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
