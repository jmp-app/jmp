FROM php:7.3.6-fpm

ARG APP_ENV

WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    zip \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy composer files
COPY api/composer*.json /var/www/

# Set permissions for user www
RUN chown www:www .

# Change current user to www
USER www

# Install dependecies form composer
# --classmap-authoritative increases performance (for productive environments only)
RUN if [ "$APP_ENV" = "prod" ]; \
    then echo "classmap"; composer install --classmap-authoritative; \
    else echo "dev"; composer install; \
    fi

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
