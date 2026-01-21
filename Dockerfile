FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

USER root

# ИСПРАВЛЕНО: убрали "8.4", используем универсальное имя
RUN apt-get update && apt-get install -y php-intl

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data
