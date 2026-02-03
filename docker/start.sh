#!/bin/bash

# Exit on error
set -e

# Cache configuration, routes, and views
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (force for production)
echo "Running migrations..."
php artisan migrate --force

# Remove the default nginx index page if it exists to avoid conflicts
rm -f /var/www/html/index.nginx-debian.html

# Start Supervisor
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
