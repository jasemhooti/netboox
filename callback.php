<?php
require_once 'config.php';
require_once 'payment.php';

// دریافت پارامترهای ارسالی از درگاه پرداخت
$authority = $_GET['Authority'] ?? '';
$status = $_GET['Status'] ?? '';
$ref_id = $_GET['RefID'] ?? '';

if ($status === 'OK' && $authority) {
    // بررسی وضعیت پرداخت
    $payment_status = checkPaymentStatus($authority);
    
    if ($payment_status === 'pending') {
        // اعمال پرداخت موفق
        if (applySuccessfulPayment($authority)) {
            // ارسال پیام موفقیت به کاربر
            $message = "پرداخت شما با موفقیت انجام شد.\n";
            $message .= "کد پیگیری: " . $ref_id;
            sendPaymentMessage($user_id, $message);
            
            // ارسال پیام به ادمین
            $admin_message = "پرداخت جدید:\n";
            $admin_message .= "مبلغ: " . $amount . " تومان\n";
            $admin_message .= "کد پیگیری: " . $ref_id;
            sendPaymentMessage(ADMIN_ID, $admin_message);
            
            // نمایش صفحه موفقیت
            echo "<h1>پرداخت با موفقیت انجام شد</h1>";
            echo "<p>کد پیگیری: " . $ref_id . "</p>";
            echo "<p>لطفا به تلگرام خود برگردید.</p>";
        } else {
            echo "<h1>خطا در ثبت پرداخت</h1>";
            echo "<p>لطفا با پشتیبانی تماس بگیرید.</p>";
        }
    } else {
        echo "<h1>پرداخت قبلاً ثبت شده است</h1>";
        echo "<p>لطفا به تلگرام خود برگردید.</p>";
    }
} else {
    echo "<h1>پرداخت ناموفق</h1>";
    echo "<p>لطفا دوباره تلاش کنید.</p>";
} 