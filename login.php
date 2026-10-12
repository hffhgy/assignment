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
<?php $currentPage = 'login'; require __DIR__ . '/navbar.php'; ?>
<main class="auth-main">
    <div class="auth-layout">
        <!-- الجزء ده يشرح وظيفة الصفحة من غير زحمة بصرية. -->
        <section class="auth-intro">
            <p class="section-kicker">Member access</p>
            <h1>Keep your account in one clear place.</h1>
            <p class="intro-copy">سجّل دخولك وشوف بيانات حسابك من Dashboard خاصة بيك. كل حاجة هنا مبنية على PHP وPDO وSession حقيقية.</p>

            <div class="intro-points">
                <div class="intro-point">
                    <span class="point-number">01</span>
                    <div><strong>Secure sign in</strong><small>الباسورد بتتراجع باستخدام password_verify.</small></div>
                </div>
                <div class="intro-point">
                    <span class="point-number">02</span>
                    <div><strong>Private dashboard</strong><small>الصفحة لا تفتح من غير Session صحيحة.</small></div>
                </div>
                <div class="intro-point">
                    <span class="point-number">03</span>
                    <div><strong>Simple by design</strong><small>واجهة واضحة من غير عناصر زيادة ملهاش لازمة.</small></div>
                </div>
            </div>
        </section>

        <section class="auth-card">
            <div class="form-heading">
                <p class="section-kicker">Sign in</p>
                <h2>Welcome back.</h2>
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

                <button type="submit" class="primary-button">
                    <span>Login</span>
                    <span aria-hidden="true">→</span>
                </button>
            </form>

            <div class="form-meta"><span>PDO connection</span><span>Protected session</span></div>
            <p class="form-footer">Don't have an account? <a href="register.php">Create one</a></p>
        </section>
    </div>
</main>
</body>
</html>
