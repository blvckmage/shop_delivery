# 🛒 Delivery Shop v2

Интернет-магазин доставки продуктов с админ-панелью, системой заказов и курьерской доставкой.

## 📋 Функционал

### Для клиентов:
- 🏪 Каталог товаров с категориями
- 🛒 Корзина с возможностью изменения количества
- 📦 Оформление заказов с доставкой
- 💬 Чат с поддержкой
- 👤 Личный кабинет

### Для администраторов:
- 📊 Дашборд с статистикой
- 📦 Управление товарами и категориями
- 👥 Управление пользователями и ролями
- 🚗 Отслеживание курьеров на карте
- 💬 Чат с клиентами

### Для курьеров:
- 📍 GPS трекинг местоположения
- 📋 Список доступных заказов
- 🔄 Изменение статуса заказа

### Для сборщиков:
- 📋 Список заказов для сборки
- ✅ Отметка о готовности заказа

## 🛠 Технологии

- **Backend**: PHP 8.2 (MVC архитектура)
- **Database**: MySQL 8.0
- **Frontend**: Tailwind CSS, vanilla JavaScript
- **Maps**: Leaflet (OpenStreetMap)

## 📁 Структура проекта

```
delivery_shop_v2/
├── app/
│   ├── Controllers/    # Контроллеры
│   ├── Core/          # Ядро приложения
│   ├── Models/        # Модели данных
│   └── Router/        # Маршрутизатор
├── database/
│   └── schema.sql     # Схема базы данных
├── templates/         # HTML шаблоны
├── uploads/           # Загруженные файлы
├── public/            # Публичные файлы
├── .env               # Конфигурация окружения
├── index.php          # Точка входа
└── Dockerfile         # Docker конфигурация
```

## 🚀 Деплой

### Вариант 1: Render.com (рекомендуется)

1. **Создайте аккаунт на [Render.com](https://render.com)**

2. **Подключите GitHub репозиторий**

3. **Создайте MySQL базу данных:**
   - New → MySQL → выберите имя `delivery-shop-db`
   - Сохраните параметры подключения

4. **Создайте веб-сервис:**
   - New → Web Service → подключите репозиторий
   - Environment: Docker
   - Добавьте переменные окружения:
     ```
     DB_HOST=<внутренний-хост-базы>
     DB_PORT=3306
     DB_NAME=delivery_shop
     DB_USER=delivery_user
     DB_PASS=<пароль-базы>
     JWT_SECRET=<случайная-строка-32+символа>
     ```

5. **Инициализируйте базу данных:**
   - Выполните SQL из `database/schema.sql` через Render Dashboard

### Вариант 2: VPS (Ubuntu)

```bash
# 1. Установите необходимые пакеты
sudo apt update
sudo apt install nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml

# 2. Клонируйте проект
git clone https://github.com/ваш-репозиторий/delivery_shop_v2.git
cd delivery_shop_v2

# 3. Создайте базу данных
sudo mysql -e "CREATE DATABASE delivery_shop;"
sudo mysql -e "CREATE USER 'delivery_user'@'localhost' IDENTIFIED BY 'secure_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON delivery_shop.* TO 'delivery_user'@'localhost';"
sudo mysql delivery_shop < database/schema.sql

# 4. Настройте .env
cp .env.example .env
nano .env  # Заполните реальные данные

# 5. Настройте права доступа
chmod -R 755 uploads/
chown -R www-data:www-data uploads/

# 6. Настройте Nginx (см. конфиг ниже)
```

**Nginx конфигурация:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/delivery_shop_v2;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(env|git) {
        deny all;
    }
}
```

### Вариант 3: Docker

```bash
# Сборка образа
docker build -t delivery-shop .

# Запуск с MySQL
docker network create delivery-network

docker run -d \
  --name delivery-mysql \
  --network delivery-network \
  -e MYSQL_ROOT_PASSWORD=rootpass \
  -e MYSQL_DATABASE=delivery_shop \
  -e MYSQL_USER=delivery_user \
  -e MYSQL_PASSWORD=userpass \
  mysql:8.0

docker run -d \
  --name delivery-shop \
  --network delivery-network \
  -p 80:80 \
  -e DB_HOST=delivery-mysql \
  -e DB_PORT=3306 \
  -e DB_NAME=delivery_shop \
  -e DB_USER=delivery_user \
  -e DB_PASS=userpass \
  delivery-shop
```

## ⚙️ Конфигурация

### .env файл

```env
# Приложение
APP_NAME=Delivery Shop
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# База данных
DB_HOST=localhost
DB_PORT=3306
DB_NAME=delivery_shop
DB_USER=delivery_user
DB_PASS=your_secure_password

# JWT (для API)
JWT_SECRET=your_random_jwt_secret_key_here

# Почта (для уведомлений)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
```

## 🔒 Безопасность

- ✅ PDO с подготовленными запросами (защита от SQL-инъекций)
- ✅ CSRF токены для форм
- ✅ Password hashing (bcrypt)
- ✅ Защита .env файла в .htaccess
- ✅ Заголовки безопасности (X-Frame-Options, X-XSS-Protection)

## 📱 WhatsApp уведомления через Twilio

Проект поддерживает отправку уведомлений в WhatsApp при:
- 📦 Создании нового заказа
- 🔄 Изменении статуса заказа
- 🏃 Регистрации нового курьера

### Пошаговая настройка Twilio

#### 1. Регистрация на Twilio

1. Перейдите на [Twilio Console](https://console.twilio.com)
2. Зарегистрируйтесь (бесплатный trial даёт $15.95 на тесты)
3. Подтвердите email и телефон

#### 2. Активация WhatsApp Sandbox

1. В консоли перейдите: **Messaging → Try it out → Send a WhatsApp message**
2. Отсканируйте QR-код или отправьте сообщение на указанный номер
3. После подключения вы увидите **Twilio WhatsApp номер** (обычно `+14155238886`)

#### 3. Получение учётных данных

1. Перейдите на [Dashboard](https://console.twilio.com)
2. Скопируйте:
   - **Account SID** — идентификатор аккаунта
   - **Auth Token** — нажмите "Show" чтобы увидеть

#### 4. Настройка .env

```env
# Настройки WhatsApp через Twilio
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
# Twilio WhatsApp номер (из sandbox или купленный)
TWILIO_WHATSAPP_FROM=+14155238886
# Ваш WhatsApp номер для получения уведомлений
TWILIO_WHATSAPP_TO=+77001234567
```

### Важно для Sandbox!

В режиме **Sandbox** вы можете отправлять сообщения только на подтверждённые номера. Чтобы добавить номер:

1. Перейдите в **Messaging → Settings → WhatsApp Sandbox**
2. Нажмите **"Add a new phone number"**
3. Отправьте код подтверждения с телефона

### Переход на Production

Для отправки на любые номера нужно:

1. **Купить WhatsApp номер** в Twilio (~$0.50-2/мес)
2. **Подключить WhatsApp Business API** (требуется верификация бизнеса)
3. Обновить `TWILIO_WHATSAPP_FROM` на купленный номер

### Примеры уведомлений

**Новый заказ:**
```
🛒 *НОВЫЙ ЗАКАЗ #123*

📍 *Адрес:* ул. Абая 123, кв 5
📞 *Телефон:* +7 700 123 4567

📦 *Товары:*
• Молоко 1л x2 = 800 ₸
• Хлеб белый x1 = 250 ₸

🚗 *Доставка:* 500 ₸
💰 *Итого:* 1 550 ₸

⏰ 30.03.2026 22:00
```

**Изменение статуса:**
```
🚗 *Заказ #123*
Статус: *В пути*
📍 Адрес: ул. Абая 123
⏰ 30.03.2026 22:15
```

### Тестирование

```php
// Проверка конфигурации
$whatsapp = new \App\Core\WhatsApp();
var_dump($whatsapp->getConfigStatus());

// Отправка тестового сообщения
$whatsapp->sendMessage('Тест из Delivery Shop!');
```

### Цены Twilio (ориентировочно)

| Тип | Цена |
|-----|------|
| WhatsApp Sandbox | Бесплатно (только на свой номер) |
| WhatsApp номер | ~$0.50-2/мес |
| Исходящее сообщение | ~$0.005-0.01 за сообщение |
| Входящее сообщение | Бесплатно |

## 📱 Скриншоты

<!-- Добавьте скриншоты вашего приложения -->

## 👥 Роли пользователей

| Роль | Описание |
|------|----------|
| `user` | Клиент - просмотр каталога, оформление заказов |
| `courier` | Курьер - доставка заказов, GPS трекинг |
| `picker` | Сборщик - сборка заказов |
| `admin` | Администратор - полный доступ |

## 📞 Поддержка

Если у вас есть вопросы или предложения, создайте Issue в репозитории.

## 📄 Лицензия

MIT License