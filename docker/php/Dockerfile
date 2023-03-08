FROM php:8.1-fpm

RUN apt update && apt install -y \
    zlib1g-dev libicu-dev \
    g++ git \
    zip libzip-dev \
    libmagickwand-dev \
    cron

RUN docker-php-ext-install \
    zip \
    intl \
    opcache \
    pdo pdo_mysql \
    exif

RUN pecl install apcu imagick \
    && docker-php-ext-enable apcu imagick

WORKDIR /app
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
