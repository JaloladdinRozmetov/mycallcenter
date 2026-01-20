FROM php:8.2-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libonig-dev libxml2-dev locales && \
    docker-php-ext-install pdo pdo_pgsql zip && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
