#!/bin/bash
set -e

echo "🚀 Starting entrypoint script..."

# Install Composer dependencies
composer_install() {
    if [ -f ./composer.json ] && [ ! -d ./vendor ]; then
        echo "📦 Installing Composer dependencies..."
        composer install --no-interaction --no-progress --optimize-autoloader --no-dev || \
        composer install --no-interaction --no-progress --optimize-autoloader
    else
        echo "⏭️ Skipping Composer install"
    fi
}

# Run database migrations if needed
run_migrations() {
    if [ -f /var/www/html/artisan ]; then
        echo "🔄 Running migrations..."
        php /var/www/html/artisan migrate --force || true
    fi
}

# Clear cache in production
clear_cache() {
    if [ -f /var/www/html/artisan ]; then
        echo "🗑️ Clearing cache..."
        php /var/www/html/artisan config:cache || true
        php /var/www/html/artisan route:cache || true
        php /var/www/html/artisan view:cache || true
    fi
}

# Set proper permissions
set_permissions() {
    echo "🔒 Setting permissions..."
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
}

# Main execution
main() {
    # Set permissions
    set_permissions

    # Install Composer dependencies
    composer_install

    # Run artisan commands if Laravel exists
    if [ -f /var/www/html/artisan ]; then
        clear_cache
        # run_migrations  # Uncomment if you want auto-migrations
    fi
    
    echo "✅ Entrypoint completed! Starting Apache..."
    
    # Execute the main command (apache2-foreground)
    exec "$@"
}

# Call main function with all arguments passed to the script
main "$@"