FROM php:8.0-fpm

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip

RUN docker-php-ext-install -j$(nproc) zip opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/dev/php/php.ini $PHP_INI_DIR

ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["php-fpm"]
