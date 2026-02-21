FROM php:8.2-apache

# Включаем mod_rewrite для красивых URL
RUN a2enmod rewrite

# Устанавливаем расширения PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем все файлы проекта
COPY . /var/www/html/

# Устанавливаем права доступа
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Создаем конфигурацию Apache
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        DirectoryIndex index.php index.html\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Перезапускаем Apache для применения конфигурации
CMD ["apache2-foreground"]