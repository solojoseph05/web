# Production Dockerfile for Laravel Backend (Laravel Cloud / Docker)
FROM php:8.3-fpm-alpine

# Install system dependencies
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

# Install PHP extensions required by Laravel
RUN docker-php-ext-install pdo pdo_mysql bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/workspace/backend

# Copy application files
COPY . /var/www/workspace/backend

# CRITICAL: Create all cache and storage directories with full read/write permissions
RUN mkdir -p /var/www/workspace/backend/.laravel/cache \
    /var/www/workspace/backend/bootstrap/cache \
    /var/www/workspace/backend/storage/framework/cache/data \
    /var/www/workspace/backend/storage/framework/sessions \
    /var/www/workspace/backend/storage/framework/views \
    /var/www/workspace/backend/storage/logs \
    && chmod -R 777 /var/www/workspace/backend/.laravel \
    && chmod -R 777 /var/www/workspace/backend/bootstrap/cache \
    && chmod -R 777 /var/www/workspace/backend/storage \
    && chown -R www-data:www-data /var/www/workspace/backend

# Run composer install with no-dev
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
