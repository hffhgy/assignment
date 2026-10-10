<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

// الكلاس ده مجمع كل العمليات اللي تخص المستخدم.
final class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        // بنستقبل اتصال الداتابيز بدل ما نعمل اتصال جديد جوه الكلاس.
        $this->pdo = $pdo;
    }

    public function emailExists(string $email): bool
    {
        // بنشوف هل الإيميل موجود قبل كده عشان نمنع الحسابات المكررة.
        $statement = $this->pdo->prepare(
            'SELECT id FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);

        return $statement->fetch() !== false;
    }

    public function register(string $name, string $email, string $password): bool
    {
        // ما ينفعش نخزن الباسورد زي ما هو، فلازم نعمله hash الأول.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // الـ prepared statement بيخلي بيانات المستخدم منفصلة عن SQL.
        $statement = $this->pdo->prepare(
            'INSERT INTO users (name, email, password)
             VALUES (:name, :email, :password)'
        );

        return $statement->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
        ]);
    }

    public function login(string $email, string $password): ?array
    {
        // بنجيب المستخدم عن طريق الإيميل عشان نقدر نقارن الباسورد.
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, password
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);

        $user = $statement->fetch();

        // لو المستخدم مش موجود أو الباسورد غلط، نرجع null.
        if ($user === false || !password_verify($password, $user['password'])) {
            return null;
        }

        // مش محتاجين نحتفظ بالـ hash داخل الـ session.
        unset($user['password']);

        return $user;
    }
}
