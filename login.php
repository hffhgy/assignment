<?php
declare(strict_types=1);

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/helpers.php';

// لازم نبدأ الـ Session قبل ما نستخدمها.
startSession();

// المستخدم اللي داخل بالفعل مش محتاج يشوف Login مرة ثانية.
if (isset($_SESSION['user'])) {
    redirect('dashboard.php');
}

// لو فيه Cookie قديمة، نحط الإيميل تلقائيًا في الفورم.
$email = (string) ($_COOKIE['remembered_email'] ?? '');
$errorMessage = '';
$infoMessage = isset($_GET['logout']) ? 'You are signed out. See you next time.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ناخد القيم من الفورم وننظف الإيميل.
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (!isValidCsrfToken($_POST['csrf_token'] ?? null)) {
        $errorMessage = 'Your form session expired. Please refresh and try again.';
    } elseif ($email === '' || $password === '') {
        $errorMessage = 'Enter your email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Enter a valid email address.';
    } else {
        try {
            // نستخدم User class عشان نراجع بيانات الدخول.
            $userManager = new User(getPDO());
            $user = $userManager->login($email, $password);

            if ($user === null) {
                $errorMessage = 'The email or password is not correct.';
            } elseif (!writeLoginLog($email)) {
                $errorMessage = 'The login log is not writable. Check logs.txt permissions.';
            } else {
                // بعد نجاح الدخول بنغير Session ID ونخزن بيانات المستخدم.
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'name' => (string) $user['name'],
                    'email' => (string) $user['email'],
                ];

                // نحفظ الإيميل لمدة 30 يوم عشان يظهر في Login الجاي.
                setcookie('remembered_email', $email, [
                    'expires' => time() + (60 * 60 * 24 * 30),
                    'path' => '/',
                    'secure' => isSecureConnection(),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);

                redirect('dashboard.php');
            }
        } catch (PDOException $exception) {
            $errorMessage = 'The database is unavailable. Check database.php and try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<main class="auth-card">
    <!-- اسم المشروع بشكل بسيط فوق الفورم -->
    <div class="brand-row">
        <a class="brand" href="index.php">
            <span class="brand-mark">N</span>
            <span><?= e(APP_NAME) ?></span>
        </a>
        <span class="project-label">PHP User System</span>
    </div>

    <div class="auth-heading">
        <p class="eyebrow">Welcome back</p>
        <h1>Login to your account</h1>
        <p><?= e(APP_TAGLINE) ?></p>
    </div>

    <?php if ($infoMessage !== ''): ?>
        <p class="message success" role="status"><?= e($infoMessage) ?></p>
    <?php endif; ?>

    <?php if ($errorMessage !== ''): ?>
        <p class="message error" role="alert"><?= e($errorMessage) ?></p>
    <?php endif; ?>

    <form method="post" action="login.php" class="auth-form">
        <!-- بنحط token مخفية عشان نتأكد إن الطلب جاي من الفورم بتاعتنا -->
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="field-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
        </div>

        <div class="field-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>

        <button type="submit" class="primary-button">Login</button>
    </form>

    <p class="form-footer">Don't have an account? <a href="register.php">Create one</a></p>
</main>
</body>
</html>
