<?php
declare(strict_types=1);

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/helpers.php';

$name = '';
$email = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // قبل ما نعالج البيانات، نتأكد إن الفورم فيها CSRF token صحيحة.
    if (!isValidCsrfToken($_POST['csrf_token'] ?? null)) {
        $errorMessage = 'Your form session expired. Please try again.';
    } else {
        // ناخد القيم ونشيل المسافات الزيادة من البداية والنهاية.
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $errorMessage = 'Please complete all three fields.';
        } elseif (strlen($name) > 100) {
            $errorMessage = 'Name must be 100 characters or fewer.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $errorMessage = 'Password must contain at least 8 characters.';
        } else {
            try {
                $user = new User(getPDO());

                // الأول نتأكد إن الإيميل مش مستخدم قبل كده.
                if ($user->emailExists($email)) {
                    $errorMessage = 'An account with this email already exists.';
                } elseif ($user->register($name, $email, $password)) {
                    // الحساب اتعمل، فنعمل Login تلقائي بدل ما نخلي المستخدم يكتب نفس البيانات تاني.
                    $loggedInUser = $user->login($email, $password);

                    if ($loggedInUser === null) {
                        $errorMessage = 'Account created, but automatic login failed. Please log in.';
                    } elseif (!writeLoginLog($email)) {
                        $errorMessage = 'Account created, but the login log is not writable.';
                    } else {
                        // نبدأ Session جديدة ونحط فيها بيانات المستخدم اللي دخل.
                        startSession();
                        session_regenerate_id(true);
                        $_SESSION['user'] = [
                            'id' => (int) $loggedInUser['id'],
                            'name' => (string) $loggedInUser['name'],
                            'email' => (string) $loggedInUser['email'],
                        ];

                        // نفتكر الإيميل عشان يظهر جاهز لو المستخدم فتح صفحة Login بعدين.
                        setcookie('remembered_email', $email, [
                            'expires' => time() + (60 * 60 * 24 * 30),
                            'path' => '/',
                            'secure' => isSecureConnection(),
                            'httponly' => true,
                            'samesite' => 'Lax',
                        ]);

                        // نروح للـDashboard ونوضح للمستخدم إن الحساب اتعمل بنجاح.
                        redirect('dashboard.php?registered=1');
                    }
                } else {
                    $errorMessage = 'The account could not be created. Please try again.';
                }
            } catch (PDOException $exception) {
                $errorMessage = 'The database is unavailable. Check database.php and try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<?php $currentPage = 'register'; require __DIR__ . '/navbar.php'; ?>
<main class="auth-main">
    <div class="auth-layout">
        <!-- نفس النظام لكن بنشرح للمستخدم خطوات إنشاء الحساب ببساطة. -->
        <section class="auth-intro">
            <p class="section-kicker">New member</p>
            <h1>Set up your access in a minute.</h1>
            <p class="intro-copy">اعمل حساب جديد، وبعدها هتدخل للـDashboard تلقائيًا من غير ما تعمل Login مرة ثانية.</p>

            <div class="intro-points">
                <div class="intro-point">
                    <span class="point-number">01</span>
                    <div><strong>Create your profile</strong><small>الاسم والإيميل والباسورد بيتراجعوا قبل الحفظ.</small></div>
                </div>
                <div class="intro-point">
                    <span class="point-number">02</span>
                    <div><strong>Password stays protected</strong><small>الباسورد بتتخزن Hash مش كنص عادي.</small></div>
                </div>
                <div class="intro-point">
                    <span class="point-number">03</span>
                    <div><strong>Go straight to work</strong><small>بعد التسجيل يتم إنشاء Session تلقائيًا.</small></div>
                </div>
            </div>
        </section>

        <section class="auth-card">
            <div class="form-heading">
                <p class="section-kicker">Create account</p>
                <h2>Start with your details.</h2>
                <p>Make an account and go straight to the dashboard.</p>
            </div>

            <?php if ($errorMessage !== ''): ?>
                <p class="message error" role="alert"><?= e($errorMessage) ?></p>
            <?php endif; ?>

            <form method="post" action="register.php" class="auth-form">
                <!-- token مخفية للحماية من إرسال فورم من مكان خارجي -->
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                <div class="field-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?= e($name) ?>" maxlength="100" autocomplete="name" required>
                </div>

                <div class="field-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
                </div>

                <div class="field-group">
                    <label for="password">Password <span class="field-hint">8 characters minimum</span></label>
                    <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required>
                </div>

                <button type="submit" class="primary-button">
                    <span>Create account</span>
                    <span aria-hidden="true">→</span>
                </button>
            </form>

            <div class="form-meta"><span>PDO connection</span><span>Secure password hash</span></div>
            <p class="form-footer">Already have an account? <a href="login.php">Login</a></p>
        </section>
    </div>
</main>
</body>
</html>
