# Dockerfile
FROM php:8.0-fpm

WORKDIR /var/www

COPY . .

RUN apt-get update && apt-get install -y zip unzip git \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --prefer-dist

EXPOSE 9000

CMD ["php-fpm"]