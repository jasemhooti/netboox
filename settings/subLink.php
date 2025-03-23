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

// دریافت لیست لینک‌های اشتراک
$stmt = $conn->query("SELECT * FROM subscription_links ORDER BY created_at DESC");
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

// پردازش فرم ایجاد لینک جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $duration = intval($_POST['duration']);
    $price = intval($_POST['price']);
    $description = $_POST['description'];
    
    if ($duration <= 0 || $price <= 0) {
        $error = "مدت زمان و قیمت باید بزرگتر از صفر باشند.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO subscription_links (duration, price, description) VALUES (?, ?, ?)");
            $stmt->execute([$duration, $price, $description]);
            
            $success = "لینک اشتراک با موفقیت ایجاد شد.";
            
            // به‌روزرسانی لیست
            $stmt = $conn->query("SELECT * FROM subscription_links ORDER BY created_at DESC");
            $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $error = "خطا در ایجاد لینک: " . $e->getMessage();
        }
    }
}

// پردازش حذف لینک
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    try {
        $stmt = $conn->prepare("DELETE FROM subscription_links WHERE id = ?");
        $stmt->execute([$id]);
        
        $success = "لینک با موفقیت حذف شد.";
        
        // به‌روزرسانی لیست
        $stmt = $conn->query("SELECT * FROM subscription_links ORDER BY created_at DESC");
        $links = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "خطا در حذف لینک: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت لینک‌های اشتراک - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>مدیریت لینک‌های اشتراک</h1>
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
                <h2>ایجاد لینک جدید</h2>
                
                <div class="form-group">
                    <label for="duration">مدت زمان (روز):</label>
                    <input type="number" id="duration" name="duration" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="price">قیمت (تومان):</label>
                    <input type="number" id="price" name="price" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="description">توضیحات:</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">ایجاد لینک</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>مدت زمان (روز)</th>
                            <th>قیمت (تومان)</th>
                            <th>توضیحات</th>
                            <th>تاریخ ایجاد</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($links as $link): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($link['id']); ?></td>
                                <td><?php echo number_format($link['duration']); ?></td>
                                <td><?php echo number_format($link['price']); ?></td>
                                <td><?php echo htmlspecialchars($link['description']); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($link['created_at'])); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $link['id']; ?>" class="btn btn-danger" onclick="return confirm('آیا از حذف این لینک اطمینان دارید؟')">حذف</a>
                                </td>
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