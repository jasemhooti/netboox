#!/bin/bash

# رنگ‌ها برای نمایش پیام‌ها
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}شروع به‌روزرسانی NetBoxBot...${NC}"

# پشتیبان‌گیری از فایل‌های مهم
echo -e "${YELLOW}در حال پشتیبان‌گیری از تنظیمات...${NC}"
cp config.php config.php.backup
cp .env .env.backup

# دریافت آخرین نسخه
echo -e "${YELLOW}در حال دریافت آخرین نسخه...${NC}"
git pull origin main

# بازگرداندن تنظیمات
echo -e "${YELLOW}در حال بازگرداندن تنظیمات...${NC}"
cp config.php.backup config.php
cp .env.backup .env

# به‌روزرسانی وابستگی‌ها
echo -e "${YELLOW}در حال به‌روزرسانی وابستگی‌ها...${NC}"
pip install -r requirements.txt --upgrade

# به‌روزرسانی دیتابیس
echo -e "${YELLOW}در حال به‌روزرسانی دیتابیس...${NC}"
php createDB.php

echo -e "${GREEN}به‌روزرسانی با موفقیت به پایان رسید!${NC}"
echo -e "${YELLOW}برای اعمال تغییرات، لطفا بات را مجددا راه‌اندازی کنید.${NC}" 