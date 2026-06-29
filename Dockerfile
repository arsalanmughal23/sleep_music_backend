FROM php:7.2-apache

# Switch to archive repositories
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list \
    && sed -i '/buster-updates/d' /etc/apt/sources.list

# Install dependencies
RUN apt-get update && apt-get install -y git zip unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev libicu-dev libxslt1-dev libzip-dev libonig-dev libxml2-dev libcurl4-openssl-dev libssl-dev cron && docker-php-ext-configure gd && docker-php-ext-install -j$(nproc) bcmath ctype curl dom ftp gd intl mbstring opcache pdo_mysql simplexml soap sockets xsl zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

COPY ./apache-config /etc/apache2/sites-available
RUN a2ensite laravel.conf

# Set Apache environment variables
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_PID_FILE=/var/run/apache2/apache2.pid
ENV APACHE_RUN_DIR=/var/run/apache2

# Create required directories
RUN mkdir -p ${APACHE_RUN_DIR} ${APACHE_LOCK_DIR} ${APACHE_LOG_DIR} && \
    chown -R www-data:www-data ${APACHE_RUN_DIR} ${APACHE_LOCK_DIR} ${APACHE_LOG_DIR}

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set recommended PHP.ini settings
RUN { \
    echo "memory_limit=128M"; \
    echo "max_execution_time=30"; \
    echo "max_input_time=200"; \
    echo "post_max_size=8M"; \
    echo "upload_max_filesize=8M"; \
    echo "date.timezone=UTC"; \
    } > /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

# ls -la /etc/apache2/sites-enabled/
# a2ensite laravel.conf
# a2dissite 000-default.conf
# service apache2 reload

# Grant permissions
# docker exec mysql_db mysql -u root -p${DB_ROOT_PASSWORD} -e "GRANT ALL PRIVILEGES ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'%';"
# docker exec mysql_db mysql -u root -p -e "GRANT ALL PRIVILEGES ON sleep_meditation.* TO 'db_user'@'%';"

# pv ./sleepmusic_live_12July2024.sql | docker exec -i mysql_db mysql -u db_user -pdb_password sleep_meditation