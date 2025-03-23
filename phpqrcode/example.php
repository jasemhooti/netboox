<?php
require_once 'qrlib.php';

// مثال تولید کد QR
$data = "https://example.com";
$size = 10;
$level = 'L';
$margin = 4;

// تنظیم هدر برای خروجی تصویر
header('Content-Type: image/png');

// تولید کد QR
QRcode::png($data, false, $level, $size, $margin);
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مثال کد QR - NetBoxBot1</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>مثال کد QR</h1>
            <nav>
                <a href="../index.php">خانه</a>
                <a href="../logout.php">خروج</a>
            </nav>
        </div>
        <div class="content">
            <div class="qr-example">
                <h2>نمونه کد QR</h2>
                <p>این یک نمونه کد QR است که با استفاده از کتابخانه PHP QR Code تولید شده است.</p>
                <img src="qrgen.php?data=<?php echo urlencode($data); ?>&size=<?php echo $size; ?>&level=<?php echo $level; ?>&margin=<?php echo $margin; ?>" alt="QR Code">
                <p>برای استفاده از این کتابخانه، کافیست فایل‌های زیر را در پروژه خود قرار دهید:</p>
                <ul>
                    <li>qrlib.php - کتابخانه اصلی</li>
                    <li>qrgen.php - فایل تولید کد QR</li>
                </ul>
                <p>سپس می‌توانید از طریق URL زیر کد QR تولید کنید:</p>
                <code>qrgen.php?data=YOUR_DATA&size=10&level=L&margin=4</code>
            </div>
        </div>
    </div>
</body>
</html> 