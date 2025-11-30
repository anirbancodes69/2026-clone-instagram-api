#!/bin/bash
set -e

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache config for production-like environment
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ensure storage directories exist and have proper permissions
php artisan storage:link 2>/dev/null || true

echo "Laravel application is ready!"

# Execute the main container command
exec "$@"

