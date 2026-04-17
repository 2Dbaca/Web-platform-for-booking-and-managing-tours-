# docker/php/Dockerfile

FROM php:8.1-fpm

# Установка расширений
RUN docker-php-ext-install pdo_mysql mysqli

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка дополнительных расширений для работы с Excel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

WORKDIR /var/www/html

# Настройки PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini