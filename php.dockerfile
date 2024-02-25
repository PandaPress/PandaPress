FROM php:8.1-fpm-alpine

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql