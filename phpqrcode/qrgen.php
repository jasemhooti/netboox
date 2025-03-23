<?php
require_once 'qrlib.php';

// تنظیمات اولیه
$size = isset($_GET['size']) ? intval($_GET['size']) : 10;
$level = isset($_GET['level']) ? $_GET['level'] : 'L';
$margin = isset($_GET['margin']) ? intval($_GET['margin']) : 4;
$data = isset($_GET['data']) ? $_GET['data'] : '';

// بررسی داده ورودی
if (empty($data)) {
    die('داده ورودی خالی است.');
}

// تنظیم هدر برای خروجی تصویر
header('Content-Type: image/png');

// تولید کد QR
QRcode::png($data, false, $level, $size, $margin); 