#!/bin/bash

# =====================================================
# Миграция данных из локальной MySQL в Railway
# =====================================================

echo "=== MySQL Migration to Railway ==="

# Настройки локальной базы (измените если нужно)
LOCAL_DB_HOST="localhost"
LOCAL_DB_PORT="3306"
LOCAL_DB_NAME="delivery_shop"
LOCAL_DB_USER="root"
LOCAL_DB_PASS=""

# Настройки Railway (заполните)
RAILWAY_DB_HOST=""  # interchange.proxy.rlwy.net
RAILWAY_DB_PORT=""  # 49434
RAILWAY_DB_NAME="railway"
RAILWAY_DB_USER="root"
RAILWAY_DB_PASS=""  # ваш пароль

# Проверка настроек
if [ -z "$RAILWAY_DB_HOST" ] || [ -z "$RAILWAY_DB_PASS" ]; then
    echo "ERROR: Заполните RAILWAY_DB_HOST и RAILWAY_DB_PASS в скрипте"
    echo ""
    echo "Получите данные из Railway:"
    echo "1. Откройте Railway проект"
    echo "2. Найдите MySQL сервис"
    echo "3. Скопируйте переменные MYSQL_HOST, MYSQL_PORT, MYSQL_PASSWORD"
    exit 1
fi

echo "Локальная база: $LOCAL_DB_HOST:$LOCAL_DB_PORT/$LOCAL_DB_NAME"
echo "Railway база: $RAILWAY_DB_HOST:$RAILWAY_DB_PORT/$RAILWAY_DB_NAME"
echo ""
read -p "Продолжить? (y/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Отменено"
    exit 1
fi

# 1. Экспорт данных из локальной базы
echo ""
echo "=== Шаг 1: Экспорт данных из локальной базы ==="

mysqldump -h"$LOCAL_DB_HOST" -P"$LOCAL_DB_PORT" -u"$LOCAL_DB_USER" ${LOCAL_DB_PASS:+-p"$LOCAL_DB_PASS"} \
    --no-create-info \
    --complete-insert \
    --skip-extended-insert \
    $LOCAL_DB_NAME > /tmp/local_data.sql 2>/dev/null

if [ $? -ne 0 ]; then
    echo "ERROR: Не удалось экспортировать данные"
    echo "Проверьте настройки локальной базы"
    exit 1
fi

echo "Экспортировано: $(wc -l < /tmp/local_data.sql) строк"

# 2. Импорт в Railway
echo ""
echo "=== Шаг 2: Импорт в Railway ==="

mysql -h"$RAILWAY_DB_HOST" -P"$RAILWAY_DB_PORT" -u"$RAILWAY_DB_USER" -p"$RAILWAY_DB_PASS" \
    $RAILWAY_DB_NAME < /tmp/local_data.sql

if [ $? -ne 0 ]; then
    echo "ERROR: Не удалось импортировать данные в Railway"
    exit 1
fi

echo ""
echo "=== Миграция завершена! ==="
echo "Проверьте данные на Railway"

# Очистка
rm -f /tmp/local_data.sql