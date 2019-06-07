FROM php:7.3-fpm-alpine

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy certificates
COPY certs.pem .
# Make certificates read-only
RUN chmod 444 /var/www/certs.pem
# Set cafile
ENV COMPOSER_CAFILE /var/www/certs.pem

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
