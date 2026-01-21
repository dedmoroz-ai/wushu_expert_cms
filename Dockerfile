FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

USER root

# Устанавливаем расширение intl, которое нужно для Filament
RUN apt-get update && apt-get install -y php8.4-intl

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data
