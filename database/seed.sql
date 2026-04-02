-- =====================================================
-- Delivery Shop v2 - Seed Data
-- =====================================================

-- Админ пользователь (пароль: admin123)
-- Хеш пароля генерируется при регистрации, это placeholder
INSERT INTO users (id, name, email, phone, password, role) VALUES 
(1, 'Админ', 'admin@delivery.shop', '+77777777777', '$2y$10$placeholder', 'admin')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Курьер (для тестов)
INSERT INTO users (id, name, email, phone, password, role) VALUES 
(2, 'Курьер', 'courier@delivery.shop', '+77777777778', '$2y$10$placeholder', 'courier')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Категории
INSERT INTO categories (id, name) VALUES
(1, 'Фрукты'),
(2, 'Овощи'),
(3, 'Молочные продукты'),
(4, 'Мясо'),
(5, 'Напитки'),
(6, 'Хлеб и выпечка'),
(7, 'Сладости'),
(8, 'Бакалея')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Товары
INSERT INTO products (id, name, description, price, category_id, image_url, is_weighted, weight_unit) VALUES
(1, 'Яблоки', 'Свежие яблоки сорта Гольден', 450.00, 1, '/uploads/products/apple.jpg', 1, 'кг'),
(2, 'Бананы', 'Спелые бананы из Эквадора', 550.00, 1, '/uploads/products/banana.jpg', 1, 'кг'),
(3, 'Апельсины', 'Сочные апельсины', 400.00, 1, '/uploads/products/orange.jpg', 1, 'кг'),
(4, 'Груши', 'Сладкие груши', 500.00, 1, '/uploads/products/pear.jpg', 1, 'кг'),
(5, 'Виноград', 'Виноград кишмиш', 800.00, 1, '/uploads/products/grape.jpg', 1, 'кг'),
(6, 'Помидоры', 'Свежие помидоры', 350.00, 2, '/uploads/products/tomato.jpg', 1, 'кг'),
(7, 'Огурцы', 'Хрустящие огурцы', 250.00, 2, '/uploads/products/cucumber.jpg', 1, 'кг'),
(8, 'Картофель', 'Картофель для пюре', 150.00, 2, '/uploads/products/potato.jpg', 1, 'кг'),
(9, 'Морковь', 'Свежая морковь', 120.00, 2, '/uploads/products/carrot.jpg', 1, 'кг'),
(10, 'Лук', 'Репчатый лук', 80.00, 2, '/uploads/products/onion.jpg', 1, 'кг'),
(11, 'Молоко', 'Молоко 3.2%', 180.00, 3, '/uploads/products/milk.jpg', 0, 'л'),
(12, 'Сыр', 'Сыр Российский', 650.00, 3, '/uploads/products/cheese.jpg', 1, 'кг'),
(13, 'Творог', 'Творог 5%', 300.00, 3, '/uploads/products/cottage.jpg', 0, 'кг'),
(14, 'Сметана', 'Сметана 20%', 150.00, 3, '/uploads/products/sour-cream.jpg', 0, 'шт'),
(15, 'Курица', 'Куриное филе', 900.00, 4, '/uploads/products/chicken.jpg', 1, 'кг'),
(16, 'Говядина', 'Говяжья вырезка', 1500.00, 4, '/uploads/products/beef.jpg', 1, 'кг'),
(17, 'Сосиски', 'Сосиски молочные', 450.00, 4, '/uploads/products/sausage.jpg', 0, 'шт'),
(18, 'Вода', 'Вода питьевая 1.5л', 100.00, 5, '/uploads/products/water.jpg', 0, 'шт'),
(19, 'Сок', 'Апельсиновый сок 1л', 250.00, 5, '/uploads/products/juice.jpg', 0, 'шт'),
(20, 'Хлеб', 'Хлеб белый нарезной', 120.00, 6, '/uploads/products/bread.jpg', 0, 'шт'),
(21, 'Батон', 'Батон нарезной', 100.00, 6, '/uploads/products/baguette.jpg', 0, 'шт'),
(22, 'Шоколад', 'Шоколад молочный', 200.00, 7, '/uploads/products/chocolate.jpg', 0, 'шт'),
(23, 'Печенье', 'Печенье Юбилейное', 80.00, 7, '/uploads/products/cookies.jpg', 0, 'шт'),
(24, 'Рис', 'Рис длиннозерный 1кг', 200.00, 8, '/uploads/products/rice.jpg', 0, 'шт'),
(25, 'Макароны', 'Макароны спагетти', 150.00, 8, '/uploads/products/pasta.jpg', 0, 'шт')
ON DUPLICATE KEY UPDATE name = VALUES(name);