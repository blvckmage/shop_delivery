FROM php:8.2-apache

# Включаем mod_rewrite для красивых URL
RUN a2enmod rewrite

# Устанавливаем расширения PHP
RUN docker-php-ext-install pdo pdo_mysql

# Копируем проект
COPY . /var/www/html/

# Права доступа
RUN chown -R www-data:www-data /var/www/html

# Конфигурация Apache
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom
