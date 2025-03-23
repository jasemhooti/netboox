<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ایجاد دیتابیس
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    $pdo->exec($sql);
    
    // انتخاب دیتابیس
    $pdo->exec("USE " . DB_NAME);
    
    // ایجاد جدول کاربران
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        telegram_id BIGINT UNIQUE,
        username VARCHAR(255),
        first_name VARCHAR(255),
        last_name VARCHAR(255),
        balance DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // ایجاد جدول تراکنش‌ها
    $sql = "CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        amount DECIMAL(10,2),
        type ENUM('deposit', 'withdraw', 'purchase'),
        status ENUM('pending', 'completed', 'failed'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $pdo->exec($sql);
    
    // ایجاد جدول پلن‌ها
    $sql = "CREATE TABLE IF NOT EXISTS plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        price DECIMAL(10,2),
        duration INT,
        traffic_limit BIGINT,
        status BOOLEAN DEFAULT TRUE
    )";
    $pdo->exec($sql);
    
    echo "دیتابیس و جداول با موفقیت ایجاد شدند.";
    
} catch(PDOException $e) {
    echo "خطا در ایجاد دیتابیس: " . $e->getMessage();
} 