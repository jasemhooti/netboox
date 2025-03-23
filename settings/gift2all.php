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
    $amount = intval($_POST['amount']);
    $message = $_POST['message'];
    
    if ($amount <= 0) {
        $error = "مبلغ باید بزرگتر از صفر باشد.";
    } else {
        try {
            $conn->beginTransaction();
            
            // دریافت لیست تمام کاربران
            $stmt = $conn->query("SELECT user_id FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // به‌روزرسانی اعتبار تمام کاربران
            $stmt = $conn->prepare("UPDATE users SET credit = credit + ? WHERE user_id = ?");
            foreach ($users as $user_id) {
                $stmt->execute([$amount, $user_id]);
                
                // ثبت در تاریخچه تراکنش‌ها
                $stmt2 = $conn->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, 'gift', ?)");
                $stmt2->execute([$user_id, $amount, $message]);
            }
            
            $conn->commit();
            $success = "هدیه با موفقیت به تمام کاربران ارسال شد.";
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "خطا در ارسال هدیه: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ارسال هدیه به همه - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ارسال هدیه به همه کاربران</h1>
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
                    <label for="amount">مبلغ هدیه (تومان):</label>
                    <input type="number" id="amount" name="amount" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="message">پیام هدیه:</label>
                    <textarea id="message" name="message" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">ارسال هدیه</button>
                </div>
            </form>
        </main>
        
        <footer>
            <p>تمامی حقوق محفوظ است &copy; <?php echo date('Y'); ?> NetBoxBot1</p>
        </footer>
    </div>
</body>
</html> 