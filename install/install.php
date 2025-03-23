<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];
    
    try {
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // ایجاد دیتابیس
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name");
        $pdo->exec("USE $db_name");
        
        // ایجاد جداول
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            is_admin BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS configs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            key_name VARCHAR(255) NOT NULL,
            value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // ایجاد کاربر ادمین
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, TRUE)");
        $stmt->execute([$admin_username, $hashed_password]);
        
        // ذخیره تنظیمات دیتابیس
        $config_content = "<?php
define('DB_HOST', '$db_host');
define('DB_NAME', '$db_name');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
";
        file_put_contents('../config.php', $config_content);
        
        $_SESSION['success'] = "نصب با موفقیت انجام شد!";
        header("Location: ../index.php");
        exit;
    } catch(PDOException $e) {
        $error = "خطا در نصب: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نصب NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>نصب NetBoxBot1</h1>
        </div>
        <div class="content">
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <h2>تنظیمات دیتابیس</h2>
                <div class="form-group">
                    <label for="db_host">آدرس دیتابیس:</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label for="db_name">نام دیتابیس:</label>
                    <input type="text" id="db_name" name="db_name" value="netboxbot1" required>
                </div>
                <div class="form-group">
                    <label for="db_user">نام کاربری دیتابیس:</label>
                    <input type="text" id="db_user" name="db_user" required>
                </div>
                <div class="form-group">
                    <label for="db_pass">رمز عبور دیتابیس:</label>
                    <input type="password" id="db_pass" name="db_pass" required>
                </div>
                
                <h2>حساب کاربری ادمین</h2>
                <div class="form-group">
                    <label for="admin_username">نام کاربری ادمین:</label>
                    <input type="text" id="admin_username" name="admin_username" required>
                </div>
                <div class="form-group">
                    <label for="admin_password">رمز عبور ادمین:</label>
                    <input type="password" id="admin_password" name="admin_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">نصب</button>
            </form>
        </div>
    </div>
</body>
</html> 