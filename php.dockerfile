FROM php:8.2-fpm-buster

WORKDIR /var/www/html


RUN apt-get update && apt-get install -y \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev

# Install MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb
