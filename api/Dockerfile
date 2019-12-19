FROM php:7.4.1-fpm-alpine

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Install composer
COPY --from=composer:1.8.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
