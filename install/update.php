<?php
session_start();
require_once '../config.php';

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // بروزرسانی جداول
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active'");
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL");
        
        // بروزرسانی تنظیمات
        $stmt = $pdo->prepare("INSERT IGNORE INTO configs (key_name, value) VALUES (?, ?)");
        $stmt->execute(['version', '1.0.0']);
        $stmt->execute(['maintenance_mode', 'false']);
        $stmt->execute(['max_users', '100']);
        
        $_SESSION['success'] = "بروزرسانی با موفقیت انجام شد!";
        header("Location: ../index.php");
        exit;
    } catch(PDOException $e) {
        $error = "خطا در بروزرسانی: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بروزرسانی NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>بروزرسانی NetBoxBot1</h1>
        </div>
        <div class="content">
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <h2>بروزرسانی سیستم</h2>
                <p>این عملیات موارد زیر را بروزرسانی می‌کند:</p>
                <ul>
                    <li>اضافه کردن فیلدهای جدید به جدول کاربران</li>
                    <li>بروزرسانی تنظیمات سیستم</li>
                    <li>بهینه‌سازی ساختار دیتابیس</li>
                </ul>
                
                <button type="submit" class="btn btn-primary">بروزرسانی</button>
            </form>
        </div>
    </div>
</body>
</html> 