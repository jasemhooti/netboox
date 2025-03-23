<?php
session_start();
require_once '../config.php';

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$payment_id = $_GET['id'];
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // دریافت اطلاعات پرداخت
    $stmt = $pdo->prepare("SELECT p.*, u.username, u.email 
                          FROM payments p 
                          JOIN users u ON p.user_id = u.id 
                          WHERE p.id = ?");
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$payment) {
        $_SESSION['error'] = "پرداخت مورد نظر یافت نشد.";
        header("Location: index.php");
        exit;
    }
    
    // پردازش عملیات
    switch ($action) {
        case 'verify':
            if ($payment['status'] !== 'pending') {
                $_SESSION['error'] = "این پرداخت قبلاً تایید یا رد شده است.";
                header("Location: index.php");
                exit;
            }
            
            // شروع تراکنش
            $pdo->beginTransaction();
            
            // بروزرسانی وضعیت پرداخت
            $stmt = $pdo->prepare("UPDATE payments SET status = 'completed', verified_at = NOW() WHERE id = ?");
            $stmt->execute([$payment_id]);
            
            // افزایش اعتبار کاربر
            $stmt = $pdo->prepare("UPDATE users SET credit = credit + ? WHERE id = ?");
            $stmt->execute([$payment['amount'], $payment['user_id']]);
            
            // ثبت در تاریخچه تراکنش‌ها
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, 'credit', ?)");
            $stmt->execute([$payment['user_id'], $payment['amount'], "شارژ حساب از طریق پرداخت #" . $payment_id]);
            
            // تایید تراکنش
            $pdo->commit();
            
            $_SESSION['success'] = "پرداخت با موفقیت تایید شد.";
            header("Location: index.php");
            exit;
            break;
            
        case 'reject':
            if ($payment['status'] !== 'pending') {
                $_SESSION['error'] = "این پرداخت قبلاً تایید یا رد شده است.";
                header("Location: index.php");
                exit;
            }
            
            // بروزرسانی وضعیت پرداخت
            $stmt = $pdo->prepare("UPDATE payments SET status = 'rejected', rejected_at = NOW() WHERE id = ?");
            $stmt->execute([$payment_id]);
            
            $_SESSION['success'] = "پرداخت با موفقیت رد شد.";
            header("Location: index.php");
            exit;
            break;
            
        case 'view':
            // نمایش جزئیات پرداخت
            ?>
            <!DOCTYPE html>
            <html dir="rtl" lang="fa">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>مشاهده پرداخت - NetBoxBot1</title>
                <link rel="stylesheet" href="../assets/style.css">
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>مشاهده پرداخت</h1>
                        <nav>
                            <a href="index.php">بازگشت</a>
                            <a href="../index.php">خانه</a>
                            <a href="../logout.php">خروج</a>
                        </nav>
                    </div>
                    <div class="content">
                        <div class="payment-details">
                            <h2>جزئیات پرداخت #<?php echo $payment['id']; ?></h2>
                            <table>
                                <tr>
                                    <th>کاربر:</th>
                                    <td><?php echo htmlspecialchars($payment['username']); ?></td>
                                </tr>
                                <tr>
                                    <th>ایمیل:</th>
                                    <td><?php echo htmlspecialchars($payment['email']); ?></td>
                                </tr>
                                <tr>
                                    <th>مبلغ:</th>
                                    <td><?php echo number_format($payment['amount']); ?> تومان</td>
                                </tr>
                                <tr>
                                    <th>وضعیت:</th>
                                    <td>
                                        <span class="status <?php echo $payment['status']; ?>">
                                            <?php echo $payment['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>تاریخ ایجاد:</th>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($payment['created_at'])); ?></td>
                                </tr>
                                <?php if ($payment['verified_at']): ?>
                                    <tr>
                                        <th>تاریخ تایید:</th>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($payment['verified_at'])); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($payment['rejected_at']): ?>
                                    <tr>
                                        <th>تاریخ رد:</th>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($payment['rejected_at'])); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                            
                            <?php if ($payment['status'] === 'pending'): ?>
                                <div class="actions">
                                    <a href="back.php?action=verify&id=<?php echo $payment['id']; ?>" class="btn btn-success">تایید پرداخت</a>
                                    <a href="back.php?action=reject&id=<?php echo $payment['id']; ?>" class="btn btn-danger">رد پرداخت</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            <?php
            break;
            
        default:
            $_SESSION['error'] = "عملیات نامعتبر است.";
            header("Location: index.php");
            exit;
    }
} catch(PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = "خطا در پردازش: " . $e->getMessage();
    header("Location: index.php");
    exit;
} 