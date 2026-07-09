#!/bin/bash
set -e

echo "🚀 Starting entrypoint script..."

# Set proper permissions
set_permissions() {

    # Create required directories
    mkdir -p /var/www/html/storage/{logs,app/public/uploads/{image/{sound,category},audio}}
    mkdir -p /var/www/html/storage/framework/{cache,sessions,testing,views}

    # Create log file if it doesn't exist
    if [ ! -f /var/www/html/storage/logs/laravel.log ]; then
        echo "📝 Creating laravel.log..."
        touch /var/www/html/storage/logs/laravel.log
    fi

    echo "🔒 Setting permissions..."
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    chmod 664 /var/www/html/storage/logs/laravel.log

    cp -r /var/www/html/public/images/categories/* /var/www/html/storage/app/public/uploads/image/category/
    cp -r /var/www/html/public/images/sounds/* /var/www/html/storage/app/public/uploads/image/sound/
    cp -r /var/www/html/public/audios/* /var/www/html/storage/app/public/uploads/audio/
}

# Install Composer dependencies
composer_install() {
    if [ -f /var/www/html/composer.json ] && [ ! -d /var/www/html/vendor ]; then
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

storage_link() {
    echo "Storage Linked"
    php artisan storage:link
}

# Main execution
main() {

    # Debug output to file
    # exec > >(tee -a /var/www/html/storage/logs/entrypoint.log)
    # exec 2>&1
    
    # echo "=== ENTRYPOINT DEBUG START ==="
    # echo "Current directory: $(pwd)"
    # echo "User: $(whoami)"
    # echo "Date: $(date)"


    # Set permissions
    set_permissions

    # Install Composer dependencies
    composer_install


    if [ -f /etc/mysql/init.sql ]; then
        # Wait for MySQL to be ready
        echo "Waiting for MySQL to be ready..."
        until mysql -h mysql_db -u ${DB_USERNAME} -p${DB_PASSWORD} -e "SELECT 1" &> /dev/null; do
            echo "MySQL not ready yet, waiting..."
            sleep 2
        done

        # Import SQL from web_server to mysql_db container
        echo "Importing SQL..."
        mysql -h ${DB_HOST} -u ${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} < /etc/mysql/init.sql
        rm -rf /etc/mysql/init.sql
    fi


    # Run artisan commands if Laravel exists
    if [ -f /var/www/html/artisan ]; then
        clear_cache
        storage_link
        # run_migrations  # Uncomment if you want auto-migrations

        php /var/www/html/artisan db:seed --class=CategoryAndSoundSeeder
    fi
    
    echo "✅ Entrypoint completed! Starting Apache..."
    
    # Execute the main command (apache2-foreground)
    exec "$@"
}

# Call main function with all arguments passed to the script
main "$@"