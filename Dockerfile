# Production Dockerfile for Laravel Cloud / Container Deployments
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/workspace

COPY . /var/www/workspace

# Ensure /var/www/workspace/backend/.laravel/cache and bootstrap/cache exist and are writable
RUN mkdir -p /var/www/workspace/backend/.laravel/cache \
    /var/www/workspace/backend/bootstrap/cache \
    /var/www/workspace/backend/storage/framework/cache/data \
    /var/www/workspace/backend/storage/framework/sessions \
    /var/www/workspace/backend/storage/framework/views \
    /var/www/workspace/backend/storage/logs \
    && chmod -R 777 /var/www/workspace/backend/.laravel \
    && chmod -R 777 /var/www/workspace/backend/bootstrap/cache \
    && chmod -R 777 /var/www/workspace/backend/storage \
    && chown -R www-data:www-data /var/www/workspace

WORKDIR /var/www/workspace/backend

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
