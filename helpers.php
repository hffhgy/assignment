<?php
declare(strict_types=1);

// الحاجات دي إعدادات عامة للتطبيق كله.
const APP_NAME = 'Northline Access';
const APP_TAGLINE = 'A simple PHP user system.';
const APP_TIMEZONE = 'Africa/Cairo';

date_default_timezone_set(APP_TIMEZONE);

function isSecureConnection(): bool
{
    // لو شغالين على HTTPS نفعّل secure للكوكيز.
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
}

function startSession(): void
{
    // ما نبدأش Session جديدة لو فيه واحدة شغالة بالفعل.
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    // إعدادات بسيطة تخلي Session Cookie أكثر أمانًا.
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isSecureConnection(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function e(string $value): string
{
    // بنحوّل أي قيمة لآمنة قبل عرضها داخل HTML.
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): void
{
    // بننقل المستخدم لصفحة ثانية ونوقف تنفيذ الصفحة الحالية.
    header('Location: ' . $location);
    exit;
}

function csrfToken(): string
{
    startSession();

    // الـ token دي بتتخزن في Session وبتتحط جوه الفورم.
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function isValidCsrfToken($token): bool
{
    startSession();

    // بنقارن token الفورم بالـ token اللي جوه Session.
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals((string) $_SESSION['csrf_token'], $token);
}

function firstName(string $name): string
{
    // بنجيب أول جزء من الاسم عشان نستخدمه في رسالة الترحيب.
    $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);

    return is_array($parts) && isset($parts[0]) ? $parts[0] : $name;
}

function initials(string $name): string
{
    // بنطلع أول حرفين من الاسم ونستخدمهم كاختصار بسيط في الصورة.
    $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);

    if (!is_array($parts)) {
        return '?';
    }

    $result = '';

    foreach (array_slice($parts, 0, 2) as $part) {
        $firstLetter = function_exists('mb_substr')
            ? mb_substr($part, 0, 1)
            : substr($part, 0, 1);
        $result .= strtoupper($firstLetter);
    }

    return $result !== '' ? $result : '?';
}

function writeLoginLog(string $email): bool
{
    // كل Login ناجح بيتكتب في سطر جديد مع وقت العملية.
    $line = trim($email) . ' - ' . date('Y-m-d H:i:s') . PHP_EOL;

    return file_put_contents(
        __DIR__ . '/logs.txt',
        $line,
        FILE_APPEND | LOCK_EX
    ) !== false;
}
