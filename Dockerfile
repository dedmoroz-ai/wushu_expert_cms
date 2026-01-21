FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

COPY . .

USER root

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Исправлено: используем www-data вместо webuser
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Исправлено: переключаемся на www-data
USER www-data
