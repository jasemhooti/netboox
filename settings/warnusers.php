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

// دریافت لیست کاربران با اعتبار کم
$stmt = $conn->query("
    SELECT 
        u.user_id,
        u.username,
        u.first_name,
        u.last_name,
        u.credit,
        u.status,
        u.expiry_date
    FROM users u
    WHERE u.credit < 10000
    OR u.expiry_date <= DATE_ADD(NOW(), INTERVAL 3 DAY)
    ORDER BY u.credit ASC, u.expiry_date ASC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// پردازش فرم ارسال هشدار
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $message = $_POST['message'];
    
    if (empty($message)) {
        $error = "پیام نمی‌تواند خالی باشد.";
    } else {
        try {
            // ارسال پیام به کاربر
            // اینجا کد ارسال پیام به تلگرام قرار می‌گیرد
            // برای مثال:
            // sendTelegramMessage($user_id, $message);
            
            // ثبت در تاریخچه هشدارها
            $stmt = $conn->prepare("INSERT INTO warnings (user_id, message, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$user_id, $message]);
            
            $success = "هشدار با موفقیت ارسال شد.";
        } catch (Exception $e) {
            $error = "خطا در ارسال هشدار: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>هشدار به کاربران - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>هشدار به کاربران</h1>
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
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>شناسه کاربر</th>
                            <th>نام کاربری</th>
                            <th>نام</th>
                            <th>نام خانوادگی</th>
                            <th>اعتبار (تومان)</th>
                            <th>تاریخ انقضا</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                <td><?php echo number_format($user['credit']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['expiry_date'])); ?></td>
                                <td><?php echo htmlspecialchars($user['status']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning" onclick="showWarningForm(<?php echo $user['user_id']; ?>)">ارسال هشدار</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- فرم ارسال هشدار -->
            <div id="warningForm" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>ارسال هشدار</h2>
                    <form method="POST">
                        <input type="hidden" id="warning_user_id" name="user_id">
                        
                        <div class="form-group">
                            <label for="message">پیام هشدار:</label>
                            <textarea id="message" name="message" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">ارسال</button>
                            <button type="button" class="btn btn-secondary" onclick="hideWarningForm()">انصراف</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        
        <footer>
            <p>تمامی حقوق محفوظ است &copy; <?php echo date('Y'); ?> NetBoxBot1</p>
        </footer>
    </div>
    
    <script>
        function showWarningForm(userId) {
            document.getElementById('warning_user_id').value = userId;
            document.getElementById('warningForm').style.display = 'block';
        }
        
        function hideWarningForm() {
            document.getElementById('warningForm').style.display = 'none';
        }
        
        // بستن فرم با کلیک خارج از آن
        window.onclick = function(event) {
            var modal = document.getElementById('warningForm');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 