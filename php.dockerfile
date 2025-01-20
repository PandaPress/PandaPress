FROM php:8.2-fpm-buster

# Update and install necessary packages
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# Install additional PHP extensions if needed
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Switch to www-data user
USER www-data

# Set working directory
WORKDIR /var/www/html
