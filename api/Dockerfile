FROM php:7.3-fpm-alpine

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Install composer
COPY --from=composer:1.8.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Add group and user for application. Read more at https://stackoverflow.com/a/49955098/7130107
# Set permissions for user www
RUN addgroup -g 1000 www && \
    adduser -u 1000 -D -G www www && \
    chown www:www .

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
