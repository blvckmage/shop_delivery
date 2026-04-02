#!/bin/bash

echo "=== Starting Delivery Shop ==="

# Render использует переменную PORT
if [ -n "$PORT" ]; then
    echo "Using PORT: $PORT"
    # Настройка Apache на нужный порт
    sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf 2>/dev/null || true
fi

# Создаём директорию для логов если нет
mkdir -p /var/log/app
chown www-data:www-data /var/log/app

# Функция для ожидания и инициализации MySQL в фоне
init_mysql() {
    echo "Waiting for MySQL..."
    echo "DB_HOST: $DB_HOST"
    echo "DB_PORT: $DB_PORT"
    echo "DB_NAME: $DB_NAME"
    echo "DB_USER: $DB_USER"
    
    MAX_ATTEMPTS=90
    ATTEMPT=0
    
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
        ATTEMPT=$((ATTEMPT + 1))
        if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
            echo "MySQL timeout after $MAX_ATTEMPTS attempts"
            return 1
        fi
        echo "MySQL is unavailable - sleeping ($ATTEMPT/$MAX_ATTEMPTS)"
        sleep 2
    done
    
    echo "MySQL is up!"
    
    # Инициализация БД (создание таблиц)
    if [ -f "/var/www/html/database/schema.sql" ]; then
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
            # Выполняем SQL напрямую через PHP (для совместимости с Railway)
            php -r "
                try {
                    \$pdo = new PDO(
                        'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME'),
                        getenv('DB_USER'),
                        getenv('DB_PASS')
                    );
                    \$sql = file_get_contents('/var/www/html/database/schema.sql');
                    \$pdo->exec(\$sql);
                    echo 'Schema applied successfully';
                } catch (Exception \$e) {
                    echo 'Error: ' . \$e->getMessage();
                }
            " 2>/dev/null || true
            echo "Database schema initialized!"
            
            # Заполняем начальными данными
            if [ -f "/var/www/html/database/seed.sql" ]; then
                echo "Seeding initial data..."
                php -r "
                    try {
                        \$pdo = new PDO(
                            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME'),
                            getenv('DB_USER'),
                            getenv('DB_PASS')
                        );
                        \$sql = file_get_contents('/var/www/html/database/seed.sql');
                        \$pdo->exec(\$sql);
                        echo 'Seed data applied';
                    } catch (Exception \$e) {
                        echo 'Error: ' . \$e->getMessage();
                    }
                " 2>/dev/null || true
                echo "Seed data initialized!"
            fi
        fi
    fi
    
    # Миграции
    if [ -d "/var/www/html/database/migrations" ] && [ -f "/var/www/html/database/migrations/add_cart_and_fixes.sql" ]; then
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
            echo "Applying migrations..."
            mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < /var/www/html/database/migrations/add_cart_and_fixes.sql 2>/dev/null || true
            echo "Migrations applied!"
        fi
    fi
}

# Запускаем инициализацию MySQL в фоне (чтобы не блокировать старт Apache)
init_mysql &

echo "=== Starting Apache ==="
exec "$@"
