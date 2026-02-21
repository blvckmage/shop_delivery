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

# Создаем конфигурацию Apache с правильным DocumentRoot
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        DirectoryIndex index.php index.html\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf