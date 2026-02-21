FROM php:8.2-apache

# Включаем mod_rewrite для красивых URL
RUN a2enmod rewrite

# Устанавливаем расширения PHP
RUN docker-php-ext-install pdo pdo_mysql

# Копируем проект
COPY . /var/www/html/

# Права доступа
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Конфигурация Apache с DirectoryIndex
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php index.html\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom