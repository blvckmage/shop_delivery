-- Миграция: создание таблицы chat для совместимости
-- Дата: 2026-04-01
-- Описание: Таблица для хранения сообщений чата (дублирует chat_messages для обратной совместимости)

CREATE TABLE IF NOT EXISTS chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT DEFAULT 0,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;