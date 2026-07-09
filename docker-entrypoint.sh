#!/bin/bash
set -e

echo "🚀 Starting entrypoint script..."

# Define lock file location
LOCK_FILE="/var/www/html/storage/.setup_complete"

# Function to check if setup is complete
is_setup_complete() {
    if [ -f "$LOCK_FILE" ]; then
        echo "✅ Setup already completed, skipping initialization..."
        return 0
    fi
    return 1
}

# Mark setup as complete
mark_setup_complete() {
    echo "📝 Marking setup as complete..."
    touch "$LOCK_FILE"
    chown www-data:www-data "$LOCK_FILE"
    chmod 664 "$LOCK_FILE"
}

# Set proper permissions
set_permissions() {
    mkdir -p /var/www/html/storage/{logs,app/public/uploads/{image/{sound,category},audio}}
    mkdir -p /var/www/html/storage/framework/{cache,sessions,testing,views}
    
    if [ ! -f /var/www/html/storage/logs/laravel.log ]; then
        echo "📝 Creating laravel.log..."
        touch /var/www/html/storage/logs/laravel.log
    fi
    
    echo "🔒 Setting permissions..."
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    chmod 664 /var/www/html/storage/logs/laravel.log
}

# Copy public assets to storage (only on first setup)
copy_assets() {
    echo "📁 Copying public assets to storage..."
    cp -r /var/www/html/public/images/categories/* /var/www/html/storage/app/public/uploads/image/category/ 2>/dev/null || true
    cp -r /var/www/html/public/images/sounds/* /var/www/html/storage/app/public/uploads/image/sound/ 2>/dev/null || true
    cp -r /var/www/html/public/audios/* /var/www/html/storage/app/public/uploads/audio/ 2>/dev/null || true
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

# Import database (only on first setup)
import_database() {
    if [ -f /etc/mysql/init.sql ]; then
        echo "💾 Importing database..."
        # Wait for MySQL to be ready
        until mysql -h mysql_db -u ${DB_USERNAME} -p${DB_PASSWORD} -e "SELECT 1" &> /dev/null; do
            echo "MySQL not ready yet, waiting..."
            sleep 2
        done
        
        mysql -h ${DB_HOST} -u ${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} < /etc/mysql/init.sql
        rm -rf /etc/mysql/init.sql
        echo "✅ Database imported successfully"
    else
        echo "⏭️ No init.sql found, skipping database import"
    fi
}

# Run seeders (only on first setup)
run_seeders() {
    if [ -f /var/www/html/artisan ]; then
        echo "🌱 Running seeders..."
        php /var/www/html/artisan db:seed --class=CategoryAndSoundSeeder --force
        echo "✅ Seeders completed"
    fi
}

# Clear cache
clear_cache() {
    if [ -f /var/www/html/artisan ]; then
        echo "🗑️ Clearing cache..."
        php /var/www/html/artisan config:cache || true
        php /var/www/html/artisan route:cache || true
        php /var/www/html/artisan view:cache || true
    fi
}

storage_link() {
    echo "🔗 Creating storage link..."
    php artisan storage:link
}

# Main execution
main() {
    # Always run these tasks
    set_permissions
    composer_install
    
    # Check if setup is already complete
    if ! is_setup_complete; then
        echo "🔄 Running initial setup..."
        
        # Copy assets
        copy_assets
        
        # Import database
        import_database
        
        # Run seeders
        run_seeders
        
        # Mark setup as complete
        mark_setup_complete
    fi
    
    # Always run these tasks
    if [ -f /var/www/html/artisan ]; then
        clear_cache
        storage_link
    fi
    
    echo "✅ Entrypoint completed! Starting Apache..."
    
    # Execute the main command
    exec "$@"
}

main "$@"