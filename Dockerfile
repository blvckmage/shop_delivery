FROM php:8.2-apache

# Включаем mod_rewrite для красивых URL
RUN a2enmod rewrite

# Устанавливаем расширения PHP
RUN docker-php-ext-install pdo pdo_mysql

# Копируем проект
COPY . /var/www/html/

# Копируем конфигурацию Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Права доступа
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html