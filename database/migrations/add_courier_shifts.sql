-- Миграция: Добавление таблицы смен курьеров
-- Дата: 2026-04-02

-- Таблица смен курьеров
CREATE TABLE IF NOT EXISTS courier_shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    courier_id INT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP NULL DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (courier_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_courier (courier_id),
    INDEX idx_active (is_active),
    INDEX idx_started (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;