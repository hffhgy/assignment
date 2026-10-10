<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

// بنبدأ الـ Session عشان نعرف المستخدم داخل ولا لأ.
startSession();

// لو داخل نوديه للـ Dashboard، غير كده نوديه للـ Login.
redirect(isset($_SESSION['user']) ? 'dashboard.php' : 'login.php');
