FROM php:8.2-apache

# Включаем модули Apache
RUN a2enmod rewrite headers deflate expires

# Устанавливаем расширения PHP и mysql-client
RUN apt-get update && apt-get install -y default-mysql-client && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo pdo_mysql

# Устанавливаем composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Копируем файлы зависимостей
COPY composer.json composer.lock ./

# Устанавливаем зависимости (без dev, с оптимизацией)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Копируем проект
COPY . /var/www/html/

# Копируем конфигурацию Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Создаём директорию для загрузок и логов
RUN mkdir -p /var/www/html/uploads/products && \
    mkdir -p /var/log/app

# Права доступа
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/uploads

# Скрипт запуска
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["apache2-foreground"]