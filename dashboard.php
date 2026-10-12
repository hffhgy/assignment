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
<?php $currentPage = 'dashboard'; require __DIR__ . '/navbar.php'; ?>
<main class="dashboard-main">
    <div class="dashboard-breadcrumb"><span>Workspace</span><b>/</b><strong>Dashboard</strong></div>

    <?php if ($registeredMessage !== ''): ?>
        <!-- الرسالة دي بتظهر بعد التسجيل الجديد والدخول التلقائي. -->
        <p class="message success" role="status"><?= e($registeredMessage) ?></p>
    <?php endif; ?>

    <section class="dashboard-intro">
        <div>
            <p class="section-kicker">Account overview</p>
            <h1>Good to see you, <?= e(firstName($displayName)) ?>.</h1>
            <p>دي مساحتك الخاصة. بيانات الصفحة جاية من الـSession بعد تسجيل الدخول.</p>
        </div>
        <div class="session-status"><span class="status-dot"></span><span>Active session</span></div>
    </section>

    <div class="dashboard-grid">
        <!-- الكارت الرئيسي لبيانات المستخدم. -->
        <section class="panel profile-panel">
            <div class="panel-header">
                <div>
                    <p class="panel-kicker">Account profile</p>
                    <h2>Your details</h2>
                </div>
                <span class="status-badge">Verified</span>
            </div>

            <div class="profile-summary">
                <div class="profile-avatar"><?= e(initials($displayName)) ?></div>
                <div>
                    <h3><?= e($displayName) ?></h3>
                    <p><?= e($displayEmail) ?></p>
                </div>
            </div>

            <dl class="details-list">
                <div><dt>Full name</dt><dd><?= e($displayName) ?></dd></div>
                <div><dt>Email address</dt><dd><?= e($displayEmail) ?></dd></div>
                <div><dt>Access level</dt><dd>Standard member</dd></div>
                <div><dt>Session state</dt><dd>Authenticated</dd></div>
            </dl>
        </section>

        <!-- الجزء ده بيوضح الوظائف الحقيقية الموجودة في المشروع. -->
        <aside class="panel system-panel">
            <div class="panel-header">
                <div>
                    <p class="panel-kicker">System status</p>
                    <h2>Everything is ready</h2>
                </div>
            </div>

            <ul class="status-list">
                <li><span class="status-number">01</span><div><strong>Session protected</strong><small>Dashboard access is checked.</small></div><b>OK</b></li>
                <li><span class="status-number">02</span><div><strong>PDO connected</strong><small>User data comes from MySQL.</small></div><b>OK</b></li>
                <li><span class="status-number">03</span><div><strong>Login logging</strong><small>Successful logins go to logs.txt.</small></div><b>ON</b></li>
            </ul>
        </aside>
    </div>

    <section class="panel project-panel">
        <div class="panel-header">
            <div>
                <p class="panel-kicker">Project components</p>
                <h2>What this system demonstrates</h2>
            </div>
            <span class="panel-note">PHP fundamentals</span>
        </div>

        <div class="component-list">
            <div><span class="component-code">PDO</span><p><strong>Database access</strong><small>Prepared statements for user operations.</small></p></div>
            <div><span class="component-code">OOP</span><p><strong>User class</strong><small>Registration and login logic in User.php.</small></p></div>
            <div><span class="component-code">FILE</span><p><strong>Login history</strong><small>Email and time are appended to logs.txt.</small></p></div>
        </div>
    </section>
</main>
</body>
</html>
