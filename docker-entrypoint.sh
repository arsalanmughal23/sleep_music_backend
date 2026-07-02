#!/bin/bash
set -e

echo "🚀 Starting entrypoint script..."

# Function to wait for MySQL
wait_for_mysql() {
    echo "⏳ Waiting for MySQL to be ready..."
    while ! nc -z mysql_db 3306; do
        sleep 1
    done
    echo "✅ MySQL is ready!"
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
    # Optional: Wait for database
    # wait_for_mysql
    
    # Set permissions
    set_permissions
    
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