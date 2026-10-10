-- بنعمل الداتابيز لو مش موجودة عندنا قبل كده.
CREATE DATABASE IF NOT EXISTS php_user_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- من هنا أي أوامر هتتنفذ على الداتابيز دي.
USE php_user_system;

-- جدول المستخدمين: كل مستخدم له id واسم وإيميل وباسورد مشفّر.
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
