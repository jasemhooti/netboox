<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

$telegram = new Api(BOT_TOKEN);

// تنظیم منطقه زمانی
date_default_timezone_set(TIMEZONE);

// تابع اصلی برای پردازش پیام‌ها
function processMessage($message) {
    global $telegram;
    
    $chat_id = $message['chat']['id'];
    $text = $message['text'] ?? '';
    $user_id = $message['from']['id'];
    
    // بررسی دستورات
    switch($text) {
        case '/start':
            sendWelcomeMessage($chat_id);
            break;
            
        case '/help':
            sendHelpMessage($chat_id);
            break;
            
        case '/balance':
            showBalance($chat_id, $user_id);
            break;
            
        case '/plans':
            showPlans($chat_id);
            break;
            
        case '/support':
            sendSupportMessage($chat_id);
            break;
            
        default:
            handleCustomMessage($chat_id, $text);
    }
}

// ارسال پیام خوش‌آمدگویی
function sendWelcomeMessage($chat_id) {
    global $telegram;
    
    $keyboard = new Keyboard([
        ['/plans', '/balance'],
        ['/help', '/support']
    ]);
    
    $message = "به NetBoxBot خوش آمدید!\n\n";
    $message .= "برای مشاهده پلن‌های موجود از دستور /plans استفاده کنید.\n";
    $message .= "برای مشاهده موجودی از دستور /balance استفاده کنید.\n";
    $message .= "برای راهنمایی از دستور /help استفاده کنید.";
    
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $message,
        'reply_markup' => $keyboard
    ]);
}

// ارسال پیام راهنما
function sendHelpMessage($chat_id) {
    global $telegram;
    
    $message = "راهنمای استفاده از NetBoxBot:\n\n";
    $message .= "/start - شروع کار با بات\n";
    $message .= "/help - نمایش این راهنما\n";
    $message .= "/plans - نمایش پلن‌های موجود\n";
    $message .= "/balance - نمایش موجودی\n";
    $message .= "/support - پشتیبانی\n\n";
    $message .= "برای خرید پلن، ابتدا موجودی خود را شارژ کنید و سپس از بخش پلن‌ها، پلن مورد نظر را انتخاب کنید.";
    
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $message
    ]);
}

// نمایش موجودی
function showBalance($chat_id, $user_id) {
    global $telegram;
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE telegram_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $message = "موجودی شما: " . number_format($result['balance']) . " تومان";
        } else {
            $message = "شما هنوز ثبت نام نکرده‌اید. لطفا از دستور /start استفاده کنید.";
        }
    } catch(PDOException $e) {
        $message = "خطا در دریافت اطلاعات. لطفا دوباره تلاش کنید.";
    }
    
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $message
    ]);
}

// نمایش پلن‌ها
function showPlans($chat_id) {
    global $telegram;
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $stmt = $pdo->query("SELECT * FROM plans WHERE status = 1");
        $plans = $stmt->fetchAll();
        
        if ($plans) {
            $message = "پلن‌های موجود:\n\n";
            foreach ($plans as $plan) {
                $message .= "نام: " . $plan['name'] . "\n";
                $message .= "قیمت: " . number_format($plan['price']) . " تومان\n";
                $message .= "مدت زمان: " . $plan['duration'] . " روز\n";
                $message .= "ترافیک: " . number_format($plan['traffic_limit']) . " مگابایت\n\n";
            }
        } else {
            $message = "در حال حاضر هیچ پلنی موجود نیست.";
        }
    } catch(PDOException $e) {
        $message = "خطا در دریافت اطلاعات. لطفا دوباره تلاش کنید.";
    }
    
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $message
    ]);
}

// ارسال پیام پشتیبانی
function sendSupportMessage($chat_id) {
    global $telegram;
    
    $message = "برای ارتباط با پشتیبانی:\n\n";
    $message .= "آیدی تلگرام: @support\n";
    $message .= "ساعات پاسخگویی: 24/7\n";
    $message .= "لطفا پیام خود را ارسال کنید تا در اسرع وقت پاسخ داده شود.";
    
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $message
    ]);
}

// پردازش پیام‌های سفارشی
function handleCustomMessage($chat_id, $text) {
    global $telegram;
    
    // اینجا می‌توانید منطق پردازش پیام‌های سفارشی را اضافه کنید
    $message = "متوجه شدم. لطفا از دستورات موجود استفاده کنید.";
    
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $message
    ]);
}

// دریافت و پردازش پیام‌های ورودی
$update = $telegram->getWebhookUpdate();

if (isset($update['message'])) {
    processMessage($update['message']);
} 