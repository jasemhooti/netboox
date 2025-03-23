<?php
session_start();
require_once '../config.php';

// بررسی دسترسی ادمین
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// دریافت تنظیمات فعلی
$stmt = $conn->prepare("SELECT * FROM settings WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// پردازش فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bot_token = $_POST['bot_token'];
    $admin_id = $_POST['admin_id'];
    $payment_token = $_POST['payment_token'];
    $min_charge = $_POST['min_charge'];
    $max_charge = $_POST['max_charge'];
    $welcome_message = $_POST['welcome_message'];
    $help_message = $_POST['help_message'];
    
    // به‌روزرسانی تنظیمات
    $stmt = $conn->prepare("UPDATE settings SET bot_token = ?, admin_id = ?, payment_token = ?, min_charge = ?, max_charge = ?, welcome_message = ?, help_message = ? WHERE id = 1");
    $stmt->execute([$bot_token, $admin_id, $payment_token, $min_charge, $max_charge, $welcome_message, $help_message]);
    
    // به‌روزرسانی متغیرهای جلسه
    $_SESSION['settings'] = [
        'bot_token' => $bot_token,
        'admin_id' => $admin_id,
        'payment_token' => $payment_token,
        'min_charge' => $min_charge,
        'max_charge' => $max_charge,
        'welcome_message' => $welcome_message,
        'help_message' => $help_message
    ];
    
    $success = "تنظیمات با موفقیت به‌روزرسانی شد.";
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنظیمات - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>تنظیمات NetBoxBot1</h1>
            <nav>
                <a href="../index.php">صفحه اصلی</a>
                <a href="../users/index.php">کاربران</a>
                <a href="../payments/index.php">پرداخت‌ها</a>
                <a href="../settings/index.php">تنظیمات</a>
                <a href="../logout.php">خروج</a>
            </nav>
        </header>
        
        <main>
            <?php if (isset($success)): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="settings-form">
                <div class="form-group">
                    <label for="bot_token">توکن بات تلگرام:</label>
                    <input type="text" id="bot_token" name="bot_token" value="<?php echo htmlspecialchars($settings['bot_token']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_id">شناسه ادمین:</label>
                    <input type="text" id="admin_id" name="admin_id" value="<?php echo htmlspecialchars($settings['admin_id']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="payment_token">توکن درگاه پرداخت:</label>
                    <input type="text" id="payment_token" name="payment_token" value="<?php echo htmlspecialchars($settings['payment_token']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="min_charge">حداقل شارژ (تومان):</label>
                    <input type="number" id="min_charge" name="min_charge" value="<?php echo htmlspecialchars($settings['min_charge']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="max_charge">حداکثر شارژ (تومان):</label>
                    <input type="number" id="max_charge" name="max_charge" value="<?php echo htmlspecialchars($settings['max_charge']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="welcome_message">پیام خوش‌آمدگویی:</label>
                    <textarea id="welcome_message" name="welcome_message" rows="4" required><?php echo htmlspecialchars($settings['welcome_message']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="help_message">پیام راهنما:</label>
                    <textarea id="help_message" name="help_message" rows="4" required><?php echo htmlspecialchars($settings['help_message']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">ذخیره تنظیمات</button>
                </div>
            </form>
        </main>
        
        <footer>
            <p>تمامی حقوق محفوظ است &copy; <?php echo date('Y'); ?> NetBoxBot1</p>
        </footer>
    </div>
</body>
</html> 