FROM php:8.3-fpm AS build

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-dev --no-interaction

# Laravel optimize
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear
RUN php artisan optimize

FROM php:8.3-fpm

WORKDIR /var/www/html

COPY --from=build /var/www/html /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
