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

// تنظیمات API ترون
$tron_api_url = 'https://api.trongrid.io';
$tron_address = 'YOUR_TRON_ADDRESS'; // آدرس کیف پول ترون
$tron_api_key = 'YOUR_API_KEY'; // کلید API ترون

// دریافت آخرین تراکنش‌ها
function getTronTransactions($address, $api_key) {
    global $tron_api_url;
    
    $url = $tron_api_url . '/v1/accounts/' . $address . '/transactions/trc20';
    $headers = [
        'TRON-PRO-API-KEY: ' . $api_key,
        'Accept: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        return json_decode($response, true);
    }
    
    return false;
}

// بررسی تراکنش‌های جدید
if (isset($_POST['check'])) {
    try {
        $transactions = getTronTransactions($tron_address, $tron_api_key);
        
        if ($transactions) {
            foreach ($transactions['data'] as $tx) {
                // بررسی اینکه آیا تراکنش قبلاً پردازش شده است
                $stmt = $conn->prepare("SELECT id FROM tron_transactions WHERE tx_id = ?");
                $stmt->execute([$tx['transaction_id']]);
                
                if (!$stmt->fetch()) {
                    // پردازش تراکنش جدید
                    $amount = $tx['value'] / 1000000; // تبدیل به USDT
                    $from = $tx['from'];
                    $to = $tx['to'];
                    
                    // ذخیره تراکنش در دیتابیس
                    $stmt = $conn->prepare("INSERT INTO tron_transactions (tx_id, from_address, to_address, amount, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$tx['transaction_id'], $from, $to, $amount]);
                    
                    // به‌روزرسانی اعتبار کاربر
                    $stmt = $conn->prepare("UPDATE users SET credit = credit + ? WHERE tron_address = ?");
                    $stmt->execute([$amount, $from]);
                }
            }
            
            $success = "تراکنش‌ها با موفقیت بررسی شدند.";
        } else {
            $error = "خطا در دریافت تراکنش‌ها از API ترون.";
        }
    } catch (Exception $e) {
        $error = "خطا در پردازش تراکنش‌ها: " . $e->getMessage();
    }
}

// دریافت لیست تراکنش‌های پردازش شده
$stmt = $conn->query("SELECT * FROM tron_transactions ORDER BY created_at DESC LIMIT 50");
$processed_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بررسی تراکنش‌های ترون - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>بررسی تراکنش‌های ترون</h1>
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
                    <button type="submit" name="check" class="btn btn-primary">بررسی تراکنش‌های جدید</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>شناسه تراکنش</th>
                            <th>از آدرس</th>
                            <th>به آدرس</th>
                            <th>مبلغ (USDT)</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($processed_transactions as $tx): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tx['tx_id']); ?></td>
                                <td><?php echo htmlspecialchars($tx['from_address']); ?></td>
                                <td><?php echo htmlspecialchars($tx['to_address']); ?></td>
                                <td><?php echo number_format($tx['amount'], 2); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($tx['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
        
        <footer>
            <p>تمامی حقوق محفوظ است &copy; <?php echo date('Y'); ?> NetBoxBot1</p>
        </footer>
    </div>
</body>
</html> 