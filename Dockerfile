FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

# Копируем файлы
COPY . .

# Переключаемся на root для установки
USER root

# Ставим зависимости
RUN composer install --no-dev --optimize-autoloader

# Чистим кеш
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Даем права на папки
RUN chown -R webuser:webuser /var/www/html/storage /var/www/html/bootstrap/cache

# Возвращаемся к пользователю
USER webuser
