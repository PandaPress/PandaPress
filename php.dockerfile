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

# Create a user with the same UID as the host user
ARG UID=1000
ARG GID=1000

# Modify www-data user and group
RUN groupmod -g $GID www-data \
    && usermod -u $UID www-data \
    && mkdir -p /var/www/html \
    && chown -R www-data:www-data /var/www/html \
    && chmod 777 /var/www/html

# Configure php-fpm to run as www-data
RUN sed -i "s/user = www-data/user = www-data/g" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/group = www-data/group = www-data/g" /usr/local/etc/php-fpm.d/www.conf

# Set working directory
WORKDIR /var/www/html


# Switch to www-data user
USER www-data
