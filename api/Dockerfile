FROM php:7.3-fpm-alpine

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
