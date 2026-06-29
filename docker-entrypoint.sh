#!/bin/bash
set -e

# Source Apache environment variables
if [ -f /etc/apache2/envvars ]; then
    . /etc/apache2/envvars
fi

# Create Laravel directories
echo "Creating Laravel storage directories..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# Set proper permissions
echo "Setting proper permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Check if this is a fresh setup (no vendor directory)
if [ ! -d "/var/www/html/vendor" ]; then
    echo "🔄 First-time setup detected. Running composer install..."
    composer install --no-interaction --optimize-autoloader
fi

# Check if .env exists, if not create from example
if [ ! -f "/var/www/html/.env" ] && [ -f "/var/www/html/.env.example" ]; then
    echo "📝 Creating .env file..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate APP_KEY if it doesn't exist or is empty
if [ -f "/var/www/html/artisan" ]; then
    APP_KEY_EXISTS=$(grep -c "^APP_KEY=" /var/www/html/.env || true)
    APP_KEY_HAS_VALUE=$(grep -c "^APP_KEY=.\+" /var/www/html/.env || true)
    
    if [ "$APP_KEY_EXISTS" -eq 0 ] || [ "$APP_KEY_HAS_VALUE" -eq 0 ]; then
        echo "🔑 Generating application key..."
        php artisan key:generate --force
    fi
fi

# Run migrations automatically (only if not in production)
if [ "${APP_ENV}" != "production" ] && [ -f "/var/www/html/artisan" ]; then
    # Check if migrations need to run
    if php artisan migrate:status 2>/dev/null | grep -q "No migrations"; then
        echo "🗄️  Running migrations..."
        php artisan migrate --force
    else
        echo "⏭️  Migrations already run. Skipping..."
    fi
fi

# Clear cache in development
if [ "${APP_ENV}" != "production" ]; then
    echo "🧹 Clearing cache..."
    php artisan config:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
fi

# Start Apache
exec "$@"