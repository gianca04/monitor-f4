FROM php:8.3-fpm

# ===============================
# System dependencies
# ===============================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    supervisor \
    nginx \
    nodejs \
    npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ===============================
# PHP extensions
# ===============================
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip

# ===============================
# PHP configuration
# ===============================
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && echo "upload_max_filesize = 100M" >> /usr/local/etc/php/php.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/php.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/php.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/php.ini

# ===============================
# Composer
# ===============================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ===============================
# App
# ===============================
WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader

# ===============================
# Frontend build
# ===============================
RUN npm install && npm run build && npm cache clean --force

# ===============================
# Permissions
# ===============================
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chown -R www-data:www-data storage bootstrap/cache

# ===============================
# Nginx & Supervisor
# ===============================
COPY nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

ENTRYPOINT ["start.sh"]
