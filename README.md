# NetBoxBot1
مشکل دارد و معلوم نیست مشکل از چیه

بات تلگرام برای مدیریت و مانیتورینگ سرور NetBox

## ویژگی‌ها

- نمایش وضعیت سرور
- نمایش لیست کاربران
- مدیریت کاربران
- مانیتورینگ سیستم

## نصب و راه‌اندازی

1. نصب وابستگی‌ها:
```bash
composer install
```

2. تنظیم فایل `.env`:
- `API_URL`: آدرس API سرور
- `API_TOKEN`: توکن API
- `TELEGRAM_TOKEN`: توکن بات تلگرام

3. اجرای بات:
```bash
php bot.php
```

## دستورات

- `/start` - شروع کار با بات
- `/help` - نمایش راهنما
- `/status` - نمایش وضعیت سرور
- `/users` - نمایش لیست کاربران

## نیازمندی‌ها

- PHP 7.4+
- MySQL 5.7+
- Composer 
