<?php

/**
 * Скрипт очистки старых данных
 * Рекомендуется запускать через cron раз в час:
 * 0 * * * * php /var/www/html/scripts/cleanup.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Core\RateLimiter;

// Загружаем переменные окружения
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim($parts[1]);
        }
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Starting cleanup...\n";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // 1. Очищаем старые rate limits
    $result = $conn->exec("DELETE FROM rate_limits WHERE first_attempt_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    echo "Cleaned rate_limits: $result rows\n";
    
    // 2. Очищаем старые сессии (неактивные более 7 дней)
    $result = $conn->exec("DELETE FROM sessions WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY))");
    echo "Cleaned sessions: $result rows\n";
    
    // 3. Очищаем старые уведомления (прочитанные более 30 дней назад)
    $result = $conn->exec("DELETE FROM notifications WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    echo "Cleaned notifications: $result rows\n";
    
    // 4. Очищаем старые сообщения чата (более 90 дней)
    $result = $conn->exec("DELETE FROM chat_messages WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
    echo "Cleaned chat_messages: $result rows\n";
    
    // 5. Очищаем временные файлы в uploads (если есть)
    $tempDir = __DIR__ . '/../uploads/temp';
    if (is_dir($tempDir)) {
        $files = glob($tempDir . '/*');
        $cleaned = 0;
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < time() - 86400) { // Старше 1 дня
                unlink($file);
                $cleaned++;
            }
        }
        echo "Cleaned temp files: $cleaned\n";
    }
    
    // 6. Оптимизация таблиц (раз в сутки)
    if (date('H') === '03') { // В 3 часа ночи
        $conn->exec("OPTIMIZE TABLE rate_limits, sessions, notifications, chat_messages");
        echo "Tables optimized\n";
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] Cleanup completed successfully\n";
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}