# Northline Access

## PHP User System — School Assignment

---

## English

### What is this project?

This is my PHP user system assignment. The idea was to take the topics from the lessons and connect them in one small project instead of making separate examples for every topic.

The project lets a user:

- Create an account with a name, email, and password, then go directly to the dashboard.
- Log in using the saved account details when returning later.
- Open a private dashboard after logging in.
- See their name and email on the dashboard.
- Keep the email remembered for the next login.
- Add a record to `logs.txt` every time the login is successful.
- Log out and lose access to the dashboard.

I kept the project small on purpose. I did not use a framework because the main point of the assignment is to show the PHP concepts directly.

---

### How I thought about the project

I started with the basic user journey:

1. The user registers.
2. The data goes to MySQL through PDO.
3. The password is hashed before it is stored.
4. PHP logs the new user in automatically and creates a session.
5. Existing users can log in normally later.
6. The dashboard checks the session before showing private data.
7. The email is saved in a cookie for convenience.
8. The login email and time are appended to `logs.txt`.
9. Logout destroys the session.

This made it easier to decide what each file should do.

---

### What each file does

| File | What it does |
|---|---|
| `index.php` | Sends the user to the correct page depending on the session. |
| `database.php` | Creates the PDO connection to MySQL. |
| `User.php` | Contains the `User` class, registration, and login methods. |
| `helpers.php` | Holds shared functions for sessions, escaping, CSRF, redirects, and logging. |
| `register.php` | Shows the registration form, creates new users, and logs them in automatically. |
| `login.php` | Checks the email and password, starts the session, saves the cookie, and writes the log. |
| `dashboard.php` | Shows the logged-in user's name and email. |
| `logout.php` | Clears the session and sends the user back to login. |
| `database.sql` | Creates the database and the `users` table. |
| `logs.txt` | Stores the email and date of each successful login. |
| `style.css` | Contains the custom design and responsive layout. |

---

### Why I used a `User` class

I used a simple class because user actions belong together. The class has three main jobs:

- `emailExists()` checks for duplicate emails.
- `register()` hashes the password and inserts the user.
- `login()` finds the user and checks the password with `password_verify()`.

The page files handle the form and the HTML, while the class handles the user database operations. It is still a simple structure, but it shows the OOP idea clearly.

---

### Database and PDO

`database.php` keeps the connection settings in one place. The project uses PDO prepared statements, so form values are sent to SQL as values instead of being joined directly into the query.

The default local settings are:

```text
Host:     127.0.0.1
Database: php_user_system
User:     root
Password: empty unless I set one in MySQL
```

If I change the MySQL `root` password in XAMPP, I must put the same password in `database.php`:

```php
const DB_PASS = 'my_mysql_password';
```

The phpMyAdmin password and the project password must match the MySQL password.

---

### Small security improvements

I added a few things beyond the basic form requirements:

- `password_hash()` keeps the original password out of the database.
- `password_verify()` checks the password during login.
- Prepared statements reduce SQL injection risk.
- User values are escaped before being printed in HTML.
- The session cookie is HTTP-only and uses `SameSite=Lax`.
- The session ID changes after a successful login.
- Login and registration forms use a CSRF token.
- `FILE_APPEND | LOCK_EX` adds to `logs.txt` without deleting previous records.

The email cookie only remembers the email. It does not prove that the user is logged in. The session is what protects the dashboard.

---

### How to run it with XAMPP

1. Copy the `php-user-system` folder into `C:\xampp\htdocs`.
2. Open the XAMPP Control Panel.
3. Start Apache and MySQL.
4. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
5. Import `database.sql`.
6. Check the database values in `database.php`.
7. Open `http://localhost/php-user-system/`.

If `root` has a password, I use that password in both MySQL/phpMyAdmin and `database.php`. If the project cannot connect, the first things to check are that MySQL is running, the database was imported, and `DB_PASS` is correct.

---

### What I tested

- Registering a new user.
- Checking that a new user goes directly to the dashboard after registration.
- Trying to register the same email twice.
- Logging in with the correct password.
- Showing an error for a wrong password.
- Checking that the email is remembered on the login page.
- Checking that a successful login is written to `logs.txt`.
- Opening the dashboard without logging in.
- Logging out and trying to open the dashboard again.
- Checking the layout on a small screen.

---

### What could be added later?

If I continued the project, I could add password reset, email verification, login rate limiting, user roles, and automated tests. I left those out because they are outside the main requirements of this school assignment.

---

## العربي

### المشروع ده عبارة عن إيه؟

ده تكليف PHP لعمل User System بسيط. الفكرة إن كل المواضيع اللي اتشرحناها تتجمع في مشروع واحد بدل ما كل موضوع يبقى مثال منفصل لوحده.

المشروع يسمح للمستخدم إنه:

- يعمل حساب بالاسم والإيميل والباسورد ويدخل الـDashboard تلقائيًا.
- يعمل Login بالبيانات اللي سجلها في المرات التالية.
- يدخل Dashboard خاصة بعد تسجيل الدخول.
- يشوف اسمه وإيميله.
- يخلي الإيميل يظهر تلقائيًا في المرة الجاية.
- يسجل كل Login ناجح في ملف `logs.txt`.
- يعمل Logout ويتمنع من دخول الـDashboard بعد كده.

أنا خليت المشروع صغير عن قصد. ما استخدمتش Framework لأن الهدف الأساسي إن استخدام PHP وOOP وPDO والـSessions والـCookies والـFile Handling يكون واضح في الكود نفسه.

---

### فكرت في المشروع إزاي؟

بدأت من رحلة المستخدم العادية:

1. المستخدم يعمل Register.
2. البيانات تروح إلى MySQL عن طريق PDO.
3. الباسورد يتعمله Hash قبل ما يتخزن.
4. PHP تعمل Login تلقائي للمستخدم الجديد وتنشئ له Session.
5. المستخدم القديم يعمل Login عادي في المرات التالية.
6. الـDashboard تتأكد من وجود الـSession قبل عرض البيانات.
7. الإيميل يتحفظ في Cookie للتسهيل فقط.
8. الإيميل والتاريخ يضافوا في `logs.txt` بعد الـLogin الناجح.
9. Logout يمسح الـSession.

بالطريقة دي كان سهل أحدد وظيفة كل ملف.

---

### كل ملف مسؤول عن إيه؟

| الملف | وظيفته |
|---|---|
| `index.php` | يوجه المستخدم للصفحة المناسبة حسب حالة الـSession. |
| `database.php` | يعمل اتصال PDO بقاعدة البيانات. |
| `User.php` | فيه كلاس `User` وعمليات Register وLogin. |
| `helpers.php` | فيه الوظائف المشتركة للـSession والـEscaping والـCSRF والـRedirect والـLog. |
| `register.php` | يعرض نموذج التسجيل وينشئ المستخدم ويدخله تلقائيًا. |
| `login.php` | يفحص البيانات ويعمل Session ويحفظ Cookie ويكتب الـLog. |
| `dashboard.php` | يعرض اسم وإيميل المستخدم الذي سجل دخوله. |
| `logout.php` | يمسح الـSession ويرجع إلى Login. |
| `database.sql` | ينشئ قاعدة البيانات وجدول `users`. |
| `logs.txt` | يخزن الإيميل والتاريخ لكل Login ناجح. |
| `style.css` | فيه التصميم والـResponsive layout. |

---

### استخدمت كلاس `User` ليه؟

استخدمت كلاس بسيط لأن العمليات الخاصة بالمستخدم مرتبطة ببعضها. الكلاس عنده ثلاث وظائف أساسية:

- `emailExists()` يتأكد إن الإيميل مش متسجل قبل كده.
- `register()` يعمل Hash للباسورد ويضيف المستخدم.
- `login()` يجيب المستخدم ويفحص الباسورد باستخدام `password_verify()`.

صفحات الـPHP مسؤولة عن الـForms والـHTML، والكلاس مسؤول عن عمليات المستخدم في الداتابيز. الهيكل بسيط، لكنه يوضح فكرة OOP بشكل حقيقي.

---

### الداتابيز وPDO

ملف `database.php` فيه إعدادات الاتصال كلها في مكان واحد. استخدمت PDO Prepared Statements، يعني بيانات الفورم بتروح للاستعلام كبيانات، مش بتتلزق مباشرة داخل SQL.

الإعدادات الافتراضية على XAMPP تكون:

```text
Host:     127.0.0.1
Database: php_user_system
User:     root
Password: فاضية إلا لو أنا عيّنت Password في MySQL
```

لو غيرت باسورد `root` في XAMPP، لازم أضع نفس الباسورد في `database.php`:

```php
const DB_PASS = 'my_mysql_password';
```

باسورد phpMyAdmin وباسورد المشروع لازم يكونوا نفس باسورد MySQL الحقيقي.

---

### إضافات أمان بسيطة

أضفت بعض الحاجات بجانب المطلوب الأساسي:

- `password_hash()` حتى لا يتم تخزين الباسورد الأصلي.
- `password_verify()` لفحص الباسورد وقت الـLogin.
- Prepared Statements لتقليل خطر SQL Injection.
- حماية القيم قبل طباعتها داخل HTML.
- جعل Session Cookie من نوع HTTP-only واستخدام `SameSite=Lax`.
- تغيير Session ID بعد تسجيل الدخول.
- استخدام CSRF Token في نماذج Login وRegister.
- استخدام `FILE_APPEND | LOCK_EX` لإضافة السجلات بدون مسح القديمة.

الـCookie تحفظ الإيميل فقط، ولا تعتبر دليلًا أن المستخدم سجل دخوله. الـSession هي التي تحمي الـDashboard.

---

### أشغل المشروع إزاي على XAMPP؟

1. انسخ مجلد `php-user-system` داخل `C:\xampp\htdocs`.
2. افتح XAMPP Control Panel.
3. شغّل Apache وMySQL.
4. افتح phpMyAdmin من `http://localhost/phpmyadmin/`.
5. اعمل Import لملف `database.sql`.
6. راجع بيانات الاتصال داخل `database.php`.
7. افتح `http://localhost/php-user-system/`.

لو `root` عليه Password، أستخدم نفس الباسورد في MySQL/phpMyAdmin وفي `database.php`. لو المشروع مش قادر يتصل، أول حاجات أراجعها هي إن MySQL شغال، وإن الداتابيز اتعملها Import، وإن `DB_PASS` صحيحة.

---

### الحاجات اللي اختبرتها

- عمل Register لمستخدم جديد.
- التأكد إن المستخدم الجديد يدخل الـDashboard مباشرة بعد التسجيل.
- تجربة تسجيل نفس الإيميل مرتين.
- Login بالباسورد الصحيحة.
- ظهور Error عند استخدام باسورد غلط.
- ظهور الإيميل تلقائيًا في صفحة Login.
- إضافة Login ناجح إلى `logs.txt`.
- محاولة فتح Dashboard بدون Login.
- عمل Logout ومحاولة فتح Dashboard مرة ثانية.
- تجربة التصميم على شاشة صغيرة.

---

### ممكن أضيف إيه بعدين؟

لو كملت المشروع، ممكن أضيف Password Reset وEmail Verification وRate Limiting وUser Roles وAutomated Tests. الحاجات دي مش موجودة لأن المطلوب في التكليف الأساسي أبسط من كده.