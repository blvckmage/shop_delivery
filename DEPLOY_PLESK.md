# Деплой на Plesk (виртуальный хостинг)

## Требования к хостингу

- **PHP**: 8.0 или выше
- **MySQL**: 5.7+ или MariaDB 10.3+
- **Apache** с модулями:
  - mod_rewrite
  - mod_headers
  - mod_deflate
  - mod_expires
- **Composer** (желательно, но не обязательно)

---

## Шаг 1: Подготовка файлов

### 1.1 Загрузка файлов на сервер

Загрузите все файлы проекта в корневую директорию сайта (обычно `httpdocs` или `public_html`):

```
httpdocs/
├── app/
├── data/
├── database/
├── public/
├── templates/
├── uploads/
├── scripts/
├── .htaccess
├── .env
├── composer.json
├── composer.lock
└── index.php
```

**Способы загрузки:**
- **FTP/SFTP** (FileZilla, WinSCP)
- **File Manager** в Plesk
- **Git** (если доступен на хостинге)

### 1.2 Установка зависимостей (если есть Composer)

Через SSH или в Plesk:
```bash
cd /var/www/vhosts/yourdomain.com/httpdocs
composer install --no-dev --optimize-autoloader
```

Если Composer недоступен - проект будет работать, так как автозагрузка уже настроена.

---

## Шаг 2: Настройка базы данных

### 2.1 Создание базы данных в Plesk

1. Откройте **Plesk Panel**
2. Перейдите в **Databases** → **Add Database**
3. Заполните:
   - **Database name**: `delivery_shop` (или с префиксом: `user_delivery_shop`)
   - **Database user**: создайте нового пользователя
   - **Password**: сгенерируйте надёжный пароль
4. Запомните или сохраните данные!

### 2.2 Импорт схемы базы данных

**Вариант A: Через phpMyAdmin**
1. В Plesk откройте **phpMyAdmin** для созданной базы
2. Перейдите во вкладку **SQL**
3. Скопируйте содержимое файла `database/schema.sql`
4. Нажмите **Go**

**Вариант B: Через Import**
1. В phpMyAdmin перейдите во вкладку **Import**
2. Выберите файл `database/schema.sql`
3. Нажмите **Go**

### 2.3 Применение миграций

После основной схемы выполните миграции из папки `database/migrations/`:
- `add_cart_and_fixes.sql`
- `add_chat_table.sql`
- `add_courier_shifts.sql`
- `add_support_admin.sql`
- `add_whatsapp_notifications.sql`

---

## Шаг 3: Настройка .env

### 3.1 Создание файла .env

Скопируйте `.env.example` в `.env` и настройте:

```env
# =====================================================
# Delivery Shop v2 - Конфигурация production
# =====================================================

# Настройки приложения
APP_ENV=production
APP_DEBUG=false

# База данных MySQL (данные из шага 2.1)
DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_db_name
DB_USER=your_db_user
DB_PASS=your_db_password

# Сессия
SESSION_LIFETIME=7200

# JWT Secret (сгенерируйте уникальный!)
JWT_SECRET=your_random_secret_key_here_min_32_chars

# WhatsApp уведомления (опционально)
TWILIO_ACCOUNT_SID=
TWILIO_AUTH_TOKEN=
TWILIO_WHATSAPP_FROM=
TWILIO_WHATSAPP_TO=
```

### 3.2 Генерация JWT_SECRET

Сгенерируйте надёжный секретный ключ:
```bash
# Linux/Mac
openssl rand -base64 32

# или
php -r "echo bin2hex(random_bytes(32));"
```

---

## Шаг 4: Настройка прав доступа

### 4.1 Установка прав через SSH или File Manager

```bash
# Папки для записи
chmod 755 uploads/
chmod 755 uploads/products/

# Защита конфиденциальных файлов
chmod 640 .env
chmod 640 .htaccess

# Запрет доступа к data/ (если используется JSON хранилище)
chmod 750 data/
```

### 4.2 Владелец файлов

Убедитесь, что владелец файлов - пользователь веб-сервера:
```bash
chown -R user:psaserv httpdocs/
chown -R user:psacln httpdocs/uploads/
```

---

## Шаг 5: Настройка Apache в Plesk

### 5.1 Проверка .htaccess

Убедитесь, что `.htaccess` загружен в корень сайта.

### 5.2 Включение HTTPS (рекомендуется)

1. В Plesk перейдите в **SSL/TLS Certificates**
2. Установите **Let's Encrypt** сертификат (бесплатный)
3. Включите **Redirect HTTP to HTTPS**

Раскомментируйте в `.htaccess` строки для HTTPS:
```apache
RewriteCond %{HTTPS} off
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 5.3 Настройка PHP в Plesk

1. Перейдите в **PHP Settings**
2. Установите:
   - **PHP version**: 8.0 или выше
   - **memory_limit**: 256M
   - **upload_max_filesize**: 10M
   - **post_max_size**: 10M
   - **max_execution_time**: 60

---

## Шаг 6: Создание администратора

### 6.1 Через phpMyAdmin

Выполните SQL запрос:

```sql
-- Создание администратора
INSERT INTO users (name, phone, password, role) 
VALUES (
    'Админ',
    '+77001234567',  -- Ваш номер телефона
    '$2y$10$...',    -- Хеш пароля (см. ниже)
    'admin'
);
```

### 6.2 Генерация хеша пароля

Создайте файл `hash.php` временно:
```php
<?php
echo password_hash('ВАШ_ПАРОЛЬ', PASSWORD_DEFAULT);
```

Выполните через SSH или браузер, скопируйте хеш, затем удалите файл.

---

## Шаг 7: Проверка работоспособности

### 7.1 Проверка

1. Откройте сайт в браузере
2. Проверьте:
   - [ ] Главная страница загружается
   - [ ] Каталог отображается
   - [ ] Можно войти как админ
   - [ ] Можно оформить заказ

### 7.2 Проверка логов при ошибках

```bash
# Логи Apache
tail -f /var/www/vhosts/yourdomain.com/logs/error_log

# Логи PHP
tail -f /var/www/vhosts/yourdomain.com/logs/php_error.log
```

---

## Шаг 8: Оптимизация для Production

### 8.1 Отключение debug режима

В `.env`:
```env
APP_ENV=production
APP_DEBUG=false
```

### 8.2 Удаление ненужных файлов

Удалите или не загружайте:
- `.git/` папку
- `.github/` папку
- `Dockerfile`
- `docker-entrypoint.sh`
- `render.yaml`
- `.editorconfig`
- Тестовые файлы

### 8.3 Оптимизация автозагрузки (если есть Composer)

```bash
composer dump-autoload --optimize --no-dev
```

---

## Шаг 9: Настройка Cron (опционально)

Для очистки старых сессий и rate limits:

В Plesk → **Scheduled Tasks**:
```
# Ежедневно в 3:00
0 3 * * * php /var/www/vhosts/yourdomain.com/httpdocs/scripts/cleanup.php
```

---

## Быстрый чек-лист

- [ ] Файлы загружены в httpdocs/
- [ ] База данных создана
- [ ] Схема импортирована (schema.sql + миграции)
- [ ] .env настроен с правильными данными БД
- [ ] JWT_SECRET сгенерирован
- [ ] Права доступа настроены (uploads/ writable)
- [ ] HTTPS включен
- [ ] PHP 8.0+ настроен
- [ ] Администратор создан
- [ ] Debug режим выключен

---

## Устранение проблем

### Ошибка 500 (Internal Server Error)

1. Проверьте права на `.env` (должен быть читаем)
2. Проверьте подключение к БД
3. Посмотрите логи ошибок

### Ошибка подключения к БД

1. Проверьте DB_HOST (обычно `localhost` или `127.0.0.1`)
2. Проверьте имя БД, пользователя и пароль
3. Убедитесь, что пользователь имеет права на БД

### Страницы не загружаются (404)

1. Проверьте наличие `.htaccess`
2. Убедитесь, что `mod_rewrite` включён
3. Проверьте `RewriteBase` в .htaccess

### Загрузка изображений не работает

1. Проверьте права на `uploads/products/` (должна быть 755 или 775)
2. Проверьте `upload_max_filesize` и `post_max_size` в PHP

---

## Контакты поддержки

При проблемах с хостингом обратитесь в поддержку вашего хостинг-провайдера.