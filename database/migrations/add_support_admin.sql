-- Добавление админа "Служба поддержки" с телефоном 7771234567
-- Пароль: admin123 (хеш bcrypt)

INSERT INTO users (name, email, phone, password, role, created_at)
SELECT 'Служба поддержки', 'support@delivery.kz', '7771234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE phone = '7771234567'
);

-- Если админ существует, но с другим телефоном - обновляем
UPDATE users 
SET phone = '7771234567', name = 'Служба поддержки'
WHERE role = 'admin' AND id = 1;