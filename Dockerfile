FROM php:8.3-cli

# Install system dependencies dan extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip mbstring xml pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install dependencies tanpa menjalankan Laravel scripts
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Generate autoloader tanpa artisan commands
RUN composer dump-autoload --optimize

# Clear cache dan optimize (jika perlu)
RUN php artisan config:cache || true
RUN php artisan route:cache || true

# Expose port
EXPOSE 8000

# Start application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]