FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    supervisor \
    nginx \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# --- FIX: Ensure PHP.ini is created and limits are applied ---
# 1. Base default config
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# 2. Append our limits explicitly to the main php.ini
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/php.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/php.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/php.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/php.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
RUN npm install && npm run build

# Create necessary directories and permissions
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    config/custom-php \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy Start script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

ENTRYPOINT ["start.sh"]
