<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

use Telegram\Bot\Api;

$telegram = new Api(BOT_TOKEN);

// تابع ایجاد درگاه پرداخت
function createPaymentGateway($amount, $user_id) {
    global $telegram;
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        
        // ایجاد تراکنش جدید
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, status) VALUES (?, ?, 'deposit', 'pending')");
        $stmt->execute([$user_id, $amount]);
        $transaction_id = $pdo->lastInsertId();
        
        // ایجاد لینک پرداخت
        $callback_url = PANEL_URL . "/payment/callback.php";
        $payment_url = "https://www.zarinpal.com/pg/StartPay/" . MERCHANT_ID . "/" . $transaction_id;
        
        return [
            'success' => true,
            'payment_url' => $payment_url,
            'transaction_id' => $transaction_id
        ];
    } catch(PDOException $e) {
        return [
            'success' => false,
            'message' => "خطا در ایجاد درگاه پرداخت"
        ];
    }
}

// تابع بررسی وضعیت پرداخت
function checkPaymentStatus($transaction_id) {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$transaction_id]);
        $transaction = $stmt->fetch();
        
        if ($transaction) {
            return $transaction['status'];
        }
        
        return null;
    } catch(PDOException $e) {
        return null;
    }
}

// تابع اعمال پرداخت موفق
function applySuccessfulPayment($transaction_id) {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        
        // دریافت اطلاعات تراکنش
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$transaction_id]);
        $transaction = $stmt->fetch();
        
        if ($transaction) {
            // به‌روزرسانی وضعیت تراکنش
            $stmt = $pdo->prepare("UPDATE transactions SET status = 'completed' WHERE id = ?");
            $stmt->execute([$transaction_id]);
            
            // افزایش موجودی کاربر
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$transaction['amount'], $transaction['user_id']]);
            
            return true;
        }
        
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

// تابع ارسال پیام به کاربر
function sendPaymentMessage($chat_id, $message) {
    global $telegram;
    
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $message
    ]);
}

// پردازش درخواست پرداخت
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action']) && $data['action'] === 'create_payment') {
        $amount = $data['amount'] ?? 0;
        $user_id = $data['user_id'] ?? 0;
        
        if ($amount > 0 && $user_id > 0) {
            $result = createPaymentGateway($amount, $user_id);
            
            if ($result['success']) {
                $message = "لینک پرداخت شما:\n" . $result['payment_url'];
                sendPaymentMessage($user_id, $message);
                
                echo json_encode([
                    'success' => true,
                    'payment_url' => $result['payment_url']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => "مقادیر نامعتبر"
            ]);
        }
    }
} 