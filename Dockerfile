FROM php:8.2-fpm

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www/html
COPY . .

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload
