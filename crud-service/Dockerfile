# # Stage 1: Build dependencies
# FROM php:8.2.12-cli AS build

# # Install system dependencies needed by Composer
# RUN apt-get update && apt-get install -y \
#     git \
#     unzip \
#     zip \
#     && docker-php-ext-install pdo pdo_mysql \
#     && rm -rf /var/lib/apt/lists/*

# # Install Composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# WORKDIR /app
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --optimize-autoloader
# COPY . .

# # Stage 2: Run app
# FROM php:8.2.12-fpm

# WORKDIR /var/www/html
# COPY --from=build /app .
# RUN docker-php-ext-install pdo pdo_mysql

# # Run Laravel development server
# CMD php artisan serve --host=0.0.0.0 --port=9000
# EXPOSE 8001

# Base image
FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libjpeg-dev libfreetype6-dev zip libonig-dev libxml2-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions required for Laravel + MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /app

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install dependencies (no-dev)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Run scripts that require artisan now
RUN composer run-script post-autoload-dump || true

# Expose port (if needed)
EXPOSE 8001

# Start command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8001"]
