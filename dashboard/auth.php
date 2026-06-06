<?php
// ---------------------------------------------------------
// HPHQ Dashboard — Session Auth Guard
// Include this at the top of every protected page.
// ---------------------------------------------------------

require_once __DIR__ . '/config.php';

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

session_start();

$authenticated = (
    isset($_SESSION['hphq_auth'], $_SESSION['hphq_expires']) &&
    $_SESSION['hphq_auth'] === true &&
    time() < $_SESSION['hphq_expires']
);

if (!$authenticated) {
    session_destroy();
    $login_url = (strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false)
        ? 'login.php'
        : '/dashboard/login.php';
    header('Location: ' . $login_url);
    exit;
}

// Slide the expiry window on activity
$_SESSION['hphq_expires'] = time() + SESSION_LIFETIME;
