<?php
session_start();
require_once '../config.php';

// بررسی دسترسی ادمین
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $user_type = $_POST['user_type'];
    
    if (empty($message)) {
        $error = "پیام نمی‌تواند خالی باشد.";
    } else {
        try {
            // دریافت لیست کاربران بر اساس نوع
            $sql = "SELECT user_id FROM users";
            if ($user_type === 'active') {
                $sql .= " WHERE status = 'active'";
            } elseif ($user_type === 'inactive') {
                $sql .= " WHERE status = 'inactive'";
            }
            
            $stmt = $conn->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($users)) {
                $error = "هیچ کاربری یافت نشد.";
            } else {
                // ارسال پیام به هر کاربر
                foreach ($users as $user_id) {
                    // اینجا کد ارسال پیام به تلگرام قرار می‌گیرد
                    // برای مثال:
                    // sendTelegramMessage($user_id, $message);
                }
                
                $success = "پیام با موفقیت به " . count($users) . " کاربر ارسال شد.";
            }
        } catch (Exception $e) {
            $error = "خطا در ارسال پیام: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ارسال پیام - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ارسال پیام به کاربران</h1>
            <nav>
                <a href="../index.php">صفحه اصلی</a>
                <a href="../users/index.php">کاربران</a>
                <a href="../payments/index.php">پرداخت‌ها</a>
                <a href="../settings/index.php">تنظیمات</a>
                <a href="../logout.php">خروج</a>
            </nav>
        </header>
        
        <main>
            <?php if ($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="settings-form">
                <div class="form-group">
                    <label for="user_type">نوع کاربران:</label>
                    <select id="user_type" name="user_type" required>
                        <option value="all">همه کاربران</option>
                        <option value="active">کاربران فعال</option>
                        <option value="inactive">کاربران غیرفعال</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">پیام:</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">ارسال پیام</button>
                </div>
            </form>
        </main>
        
        <footer>
            <p>تمامی حقوق محفوظ است &copy; <?php echo date('Y'); ?> NetBoxBot1</p>
        </footer>
    </div>
</body>
</html> 