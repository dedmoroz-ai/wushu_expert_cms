FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

USER root

# ИСПРАВЛЕНИЕ: Используем специальный установщик расширений.
# Он работает надежнее, чем apt-get для новых версий PHP.
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Устанавливаем intl через этот скрипт
RUN install-php-extensions intl

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data
