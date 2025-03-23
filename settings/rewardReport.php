<?php
session_start();
require_once '../config.php';

// بررسی دسترسی ادمین
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// دریافت تاریخ شروع و پایان
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// دریافت گزارش پاداش‌ها
$stmt = $conn->prepare("
    SELECT 
        t.user_id,
        u.username,
        u.first_name,
        u.last_name,
        COUNT(*) as total_rewards,
        SUM(t.amount) as total_amount
    FROM transactions t
    JOIN users u ON t.user_id = u.user_id
    WHERE t.type = 'reward'
    AND DATE(t.created_at) BETWEEN ? AND ?
    GROUP BY t.user_id
    ORDER BY total_amount DESC
");

$stmt->execute([$start_date, $end_date]);
$rewards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش پاداش‌ها - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>گزارش پاداش‌ها</h1>
            <nav>
                <a href="../index.php">صفحه اصلی</a>
                <a href="../users/index.php">کاربران</a>
                <a href="../payments/index.php">پرداخت‌ها</a>
                <a href="../settings/index.php">تنظیمات</a>
                <a href="../logout.php">خروج</a>
            </nav>
        </header>
        
        <main>
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="start_date">از تاریخ:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="end_date">تا تاریخ:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">نمایش گزارش</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>شناسه کاربر</th>
                            <th>نام کاربری</th>
                            <th>نام</th>
                            <th>نام خانوادگی</th>
                            <th>تعداد پاداش‌ها</th>
                            <th>مجموع مبلغ (تومان)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rewards as $reward): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reward['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($reward['username']); ?></td>
                                <td><?php echo htmlspecialchars($reward['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($reward['last_name']); ?></td>
                                <td><?php echo number_format($reward['total_rewards']); ?></td>
                                <td><?php echo number_format($reward['total_amount']); ?></td>
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