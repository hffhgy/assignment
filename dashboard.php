<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

// أي حد يفتح الصفحة لازم يكون عامل Login الأول.
startSession();

if (!isset($_SESSION['user'])) {
    redirect('login.php');
}

// البيانات دي اتخزنت في Session وقت تسجيل الدخول.
$loggedInUser = $_SESSION['user'];
$displayName = (string) ($loggedInUser['name'] ?? 'Member');
$displayEmail = (string) ($loggedInUser['email'] ?? '');
$registeredMessage = isset($_GET['registered'])
    ? 'Account created successfully. You are logged in now.'
    : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
<div class="dashboard-shell">
    <header class="dashboard-header">
        <!-- اسم المشروع في أعلى الصفحة -->
        <a class="brand" href="dashboard.php">
            <span class="brand-mark">N</span>
            <span><?= e(APP_NAME) ?></span>
        </a>

        <div class="header-account">
            <span class="mini-avatar"><?= e(initials($displayName)) ?></span>
            <span class="header-name"><?= e($displayName) ?></span>
            <a class="logout-button" href="logout.php">Logout</a>
        </div>
    </header>

    <main class="dashboard-content">
        <?php if ($registeredMessage !== ''): ?>
            <!-- الرسالة دي بتظهر مرة واحدة بعد إنشاء حساب جديد والدخول تلقائيًا -->
            <p class="message success" role="status"><?= e($registeredMessage) ?></p>
        <?php endif; ?>

        <section class="dashboard-title">
            <p class="eyebrow">Dashboard</p>
            <h1>Hello, <?= e(firstName($displayName)) ?>.</h1>
            <p>Everything is working. This is your private page.</p>
        </section>

        <!-- الكارت الرئيسي اللي فيه بيانات المستخدم -->
        <section class="profile-card">
            <div class="profile-heading">
                <div class="profile-avatar"><?= e(initials($displayName)) ?></div>
                <div>
                    <p class="card-label">Logged-in user</p>
                    <h2><?= e($displayName) ?></h2>
                    <p class="muted-copy">Your data is coming from the session.</p>
                </div>
            </div>

            <div class="profile-details">
                <div class="detail-row">
                    <span>Name</span>
                    <strong><?= e($displayName) ?></strong>
                </div>
                <div class="detail-row">
                    <span>Email</span>
                    <strong><?= e($displayEmail) ?></strong>
                </div>
            </div>
        </section>

        <!-- ملاحظة صغيرة بدل ما نحط كروت كثيرة ملهاش لازمة -->
        <p class="dashboard-note"><strong>Note:</strong> Every successful login is saved in <code>logs.txt</code>.</p>
    </main>
</div>
</body>
</html>
