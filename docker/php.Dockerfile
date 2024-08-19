# custom image based on https://dev.to/jackmiras/laravel-with-php7-4-in-an-alpine-container-3jk6
FROM php:8.1-fpm-alpine3.17

WORKDIR /var/www/html/

# Install Essentials and Packages
RUN apk add --no-cache \
    zip unzip curl mysql-client mariadb-connector-c\
    icu-dev libzip-dev && \
    rm -rf /var/cache/apk/*

# Install additional extension
RUN docker-php-ext-install intl zip pdo pdo_mysql

# Install GD
RUN apk add --no-cache \
    freetype-dev libjpeg-turbo-dev libpng-dev zlib-dev && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Installing composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm -rf composer-setup.php

# Configure PHP
RUN mkdir -p /run/php/ && touch /run/php/php8.1-fpm.pid

COPY docker/php.ini-production /usr/local/etc/php/php.ini

# Copy application files
COPY . .
RUN chown -R www-data:www-data /var/www/html/ && \
    chmod -R 775 /var/www/html/
RUN composer install --optimize-autoloader --no-dev --no-interaction


# Prepare for running
RUN php artisan clear-compiled

# Recreate boostrap/cache/compiled.php
RUN php artisan optimize

# Copy entrypoint
COPY docker/entrypoint-php.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
