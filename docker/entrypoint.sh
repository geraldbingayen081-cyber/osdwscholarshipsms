#!/bin/sh
set -e

# Create storage symbolic link
php artisan storage:link || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || true

# Seed database if seeded flag is not set or requested
if [ "$RUN_SEEDER" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force || true
fi

# Clear and optimize Laravel caches for production
echo "Caching Laravel configuration & routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground
echo "Starting Nginx web server..."
nginx -g "daemon off;"
