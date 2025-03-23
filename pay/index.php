<?php
session_start();
require_once '../config.php';

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // دریافت لیست پرداخت‌ها
    $stmt = $pdo->query("SELECT p.*, u.username 
                        FROM payments p 
                        JOIN users u ON p.user_id = u.id 
                        ORDER BY p.created_at DESC");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "خطا در دریافت اطلاعات: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت پرداخت‌ها - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>مدیریت پرداخت‌ها</h1>
            <nav>
                <a href="../index.php">خانه</a>
                <a href="../logout.php">خروج</a>
            </nav>
        </div>
        <div class="content">
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>کاربر</th>
                            <th>مبلغ</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo $payment['id']; ?></td>
                                <td><?php echo htmlspecialchars($payment['username']); ?></td>
                                <td><?php echo number_format($payment['amount']); ?> تومان</td>
                                <td>
                                    <span class="status <?php echo $payment['status']; ?>">
                                        <?php echo $payment['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($payment['created_at'])); ?></td>
                                <td>
                                    <a href="back.php?id=<?php echo $payment['id']; ?>" class="btn btn-info">مشاهده</a>
                                    <?php if ($payment['status'] === 'pending'): ?>
                                        <a href="back.php?action=verify&id=<?php echo $payment['id']; ?>" class="btn btn-success">تایید</a>
                                        <a href="back.php?action=reject&id=<?php echo $payment['id']; ?>" class="btn btn-danger">رد</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 