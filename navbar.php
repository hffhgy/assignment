<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

// الـNavbar بتقرأ الـSession عشان تعرف تعرض روابط الزائر أو المستخدم الداخل.
startSession();

$navUser = $_SESSION['user'] ?? null;
$navName = $navUser !== null ? (string) ($navUser['name'] ?? 'Account') : '';
$currentPage = $currentPage ?? '';
?>
<header class="site-header">
    <div class="site-header-inner">
        <!-- اسم المشروع ثابت في كل الصفحات عشان التنقل يبقى واضح. -->
        <a class="site-brand" href="index.php" aria-label="<?= e(APP_NAME) ?>">
            <span class="brand-mark">N</span>
            <span class="brand-copy">
                <strong><?= e(APP_NAME) ?></strong>
                <small>PHP User System</small>
            </span>
        </a>

        <nav class="site-nav" aria-label="Main navigation">
            <a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php">Home</a>

            <?php if ($navUser !== null): ?>
                <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
            <?php else: ?>
                <a class="nav-link <?= $currentPage === 'login' ? 'active' : '' ?>" href="login.php">Login</a>
                <a class="nav-action <?= $currentPage === 'register' ? 'active' : '' ?>" href="register.php">Create account</a>
            <?php endif; ?>
        </nav>

        <div class="nav-account">
            <?php if ($navUser !== null): ?>
                <span class="nav-avatar"><?= e(initials($navName)) ?></span>
                <span class="nav-user-name"><?= e($navName) ?></span>
                <a class="nav-logout" href="logout.php">Logout</a>
            <?php else: ?>
                <span class="nav-status"><span class="status-dot"></span>Local project</span>
            <?php endif; ?>
        </div>
    </div>
</header>
