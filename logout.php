<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

// لازم نفتح الـ Session الأول عشان نقدر نمسحها.
startSession();

// بنمسح كل البيانات الموجودة داخل Session.
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $sessionCookieParameters = session_get_cookie_params();

    // بننتهي من Cookie الخاصة بالـ Session كمان.
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $sessionCookieParameters['path'],
        'domain' => $sessionCookieParameters['domain'],
        'secure' => $sessionCookieParameters['secure'],
        'httponly' => $sessionCookieParameters['httponly'],
        'samesite' => $sessionCookieParameters['samesite'] ?? 'Lax',
    ]);
}

// هنا المستخدم بقى خارج الحساب ونرجعه لصفحة Login.
session_destroy();
redirect('login.php?logout=1');
