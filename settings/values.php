<?php
// تنظیمات عمومی
define('SITE_NAME', 'NetBoxBot1');
define('SITE_URL', 'https://your-domain.com');
define('ADMIN_EMAIL', 'admin@your-domain.com');

// تنظیمات دیتابیس
define('DB_HOST', 'localhost');
define('DB_NAME', 'netboxbot1');
define('DB_USER', 'root');
define('DB_PASS', '');

// تنظیمات تلگرام
define('BOT_TOKEN', 'YOUR_BOT_TOKEN');
define('ADMIN_ID', 'YOUR_ADMIN_ID');

// تنظیمات پرداخت
define('PAYMENT_TOKEN', 'YOUR_PAYMENT_TOKEN');
define('MIN_CHARGE', 10000); // حداقل مبلغ شارژ (تومان)
define('MAX_CHARGE', 1000000); // حداکثر مبلغ شارژ (تومان)

// تنظیمات ترون
define('TRON_API_URL', 'https://api.trongrid.io');
define('TRON_ADDRESS', 'YOUR_TRON_ADDRESS');
define('TRON_API_KEY', 'YOUR_API_KEY');

// تنظیمات پیام‌ها
define('WELCOME_MESSAGE', 'به NetBoxBot1 خوش آمدید!');
define('HELP_MESSAGE', 'برای استفاده از بات، لطفاً دستورات زیر را دنبال کنید:');

// تنظیمات امنیتی
define('SESSION_LIFETIME', 3600); // زمان اعتبار جلسه (ثانیه)
define('MAX_LOGIN_ATTEMPTS', 3); // حداکثر تعداد تلاش‌های ورود
define('LOGIN_TIMEOUT', 900); // زمان قفل شدن حساب (ثانیه)

// تنظیمات فایل‌ها
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 5242880); // حداکثر حجم فایل (5 مگابایت)
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// تنظیمات لاگ
define('LOG_DIR', '../logs/');
define('ERROR_LOG', LOG_DIR . 'error.log');
define('ACCESS_LOG', LOG_DIR . 'access.log');

// تنظیمات کش
define('CACHE_DIR', '../cache/');
define('CACHE_LIFETIME', 3600); // زمان اعتبار کش (ثانیه)

// تنظیمات منطقه زمانی
date_default_timezone_set('Asia/Tehran');

// تنظیمات زبان
setlocale(LC_ALL, 'fa_IR.UTF-8');

// تنظیمات نمایش خطاها
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تنظیمات اتصال به دیتابیس
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
} catch (PDOException $e) {
    die("خطا در اتصال به دیتابیس: " . $e->getMessage());
}

// تنظیمات جلسه
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict'); 