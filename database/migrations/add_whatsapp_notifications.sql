-- =====================================================
-- Миграция: Добавление WhatsApp уведомлений
-- Дата: 30.03.2026
-- =====================================================

-- Добавляем поля для WhatsApp уведомлений
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS whatsapp_notifications TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS whatsapp_phone VARCHAR(20) DEFAULT NULL;

-- Добавляем индекс для быстрого поиска подписчиков
CREATE INDEX IF NOT EXISTS idx_whatsapp_notifications ON users(whatsapp_notifications);

-- Показать результат
SELECT 'Migration completed: WhatsApp notifications fields added' AS status;