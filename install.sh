#!/bin/bash

# رنگ‌ها برای نمایش پیام‌ها
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}شروع نصب NetBoxBot...${NC}"

# بررسی وجود PHP
if ! command -v php &> /dev/null; then
    echo -e "${RED}PHP نصب نشده است. لطفا ابتدا PHP را نصب کنید.${NC}"
    exit 1
fi

# بررسی وجود MySQL
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}MySQL نصب نشده است. لطفا ابتدا MySQL را نصب کنید.${NC}"
    exit 1
fi

# ایجاد دیتابیس
echo -e "${YELLOW}در حال ایجاد دیتابیس...${NC}"
php createDB.php

# تنظیم مجوزها
echo -e "${YELLOW}در حال تنظیم مجوزها...${NC}"
chmod 755 bot.php
chmod 644 config.php

# نصب وابستگی‌ها
echo -e "${YELLOW}در حال نصب وابستگی‌ها...${NC}"
pip install -r requirements.txt

# تنظیم فایل .env
echo -e "${YELLOW}لطفا تنظیمات فایل .env را انجام دهید:${NC}"
read -p "آدرس API سرور را وارد کنید: " api_url
read -p "توکن API را وارد کنید: " api_token
read -p "توکن بات تلگرام را وارد کنید: " telegram_token

echo "API_URL=$api_url" > .env
echo "API_TOKEN=$api_token" >> .env
echo "TELEGRAM_TOKEN=$telegram_token" >> .env

echo -e "${GREEN}نصب با موفقیت به پایان رسید!${NC}"
echo -e "${YELLOW}برای شروع بات، دستور زیر را اجرا کنید:${NC}"
echo -e "python bot.py" 