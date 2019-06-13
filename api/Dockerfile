FROM php:7.3-fpm-alpine

# Install composer
COPY --from=composer:1.8.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
