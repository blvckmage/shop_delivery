#!/bin/bash
set -e

echo "=== Starting Delivery Shop ==="

# Ждём готовности MySQL
echo "Waiting for MySQL..."
until php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME'),
            getenv('DB_USER'),
            getenv('DB_PASS')
        );
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

echo "MySQL is up!"

# Инициализация БД если нужно
if [ -f "/var/www/html/database/schema.sql" ]; then
    echo "Checking database schema..."
    
    # Проверяем есть ли таблица users
    TABLE_EXISTS=$(php -r "
        try {
            \$pdo = new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME'),
                getenv('DB_USER'),
                getenv('DB_PASS')
            );
            \$result = \$pdo->query(\"SHOW TABLES LIKE 'users'\");
            echo \$result->rowCount() > 0 ? '1' : '0';
        } catch (Exception \$e) {
            echo '0';
        }
    ")
    
    if [ "$TABLE_EXISTS" = "0" ]; then
        echo "Initializing database schema..."
        mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < /var/www/html/database/schema.sql
        echo "Database schema initialized!"
    else
        echo "Database schema already exists."
    fi
fi

# Запуск миграций
if [ -d "/var/www/html/database/migrations" ]; then
    echo "Running database migrations..."
    
    # Проверяем есть ли таблица cart_items
    CART_EXISTS=$(php -r "
        try {
            \$pdo = new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME'),
                getenv('DB_USER'),
                getenv('DB_PASS')
            );
            \$result = \$pdo->query(\"SHOW TABLES LIKE 'cart_items'\");
            echo \$result->rowCount() > 0 ? '1' : '0';
        } catch (Exception \$e) {
            echo '0';
        }
    ")
    
    if [ "$CART_EXISTS" = "0" ]; then
        echo "Applying new migrations..."
        if [ -f "/var/www/html/database/migrations/add_cart_and_fixes.sql" ]; then
            mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < /var/www/html/database/migrations/add_cart_and_fixes.sql 2>/dev/null || true
            echo "Migrations applied!"
        fi
    else
        echo "Migrations already applied."
    fi
fi

# Создаём директорию для логов если нет
mkdir -p /var/log/app
chown www-data:www-data /var/log/app

# Настраиваем cron для очистки (если установлен)
if command -v crontab &> /dev/null; then
    echo "0 * * * * php /var/www/html/scripts/cleanup.php >> /var/log/app/cleanup.log 2>&1" | crontab -u www-data -
fi

echo "=== Starting Apache ==="

# Запускаем Apache
exec "$@"