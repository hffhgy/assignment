<?php
declare(strict_types=1);

// بيانات الدخول لقاعدة البيانات. لو عندك باسورد مختلفة لـ root غيّر DB_PASS فقط.
const DB_HOST = '127.0.0.1';
const DB_NAME = 'php_user_system';
const DB_USER = 'root';
const DB_PASS = 'Northline@2026';

function getPDO(): PDO
{
    // static تخلي نفس الاتصال يتعاد استخدامه بدل فتح اتصال جديد كل مرة.
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // الـ DSN هو وصف المكان اللي فيه MySQL واسم الداتابيز.
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    try {
        // هنا بنفتح اتصال PDO وبنحدد طريقة التعامل مع الأخطاء والنتائج.
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $pdo;
    } catch (PDOException $exception) {
        // بنعرض السبب الحقيقي أثناء التجربة عشان نعرف المشكلة منين.
        exit('Database connection failed: ' . $exception->getMessage());
    }
}
