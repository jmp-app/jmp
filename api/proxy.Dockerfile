FROM php:7.3-fpm-alpine

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy certificates and set environment variable
COPY Dockerfile certs.pem* ./
RUN if [[ -f /var/www/certs.pem ]]; then \
        export COMPOSER_CAFILE=/var/www/certs.pem; \
    fi;
ENV COMPOSER_CAFILE $COMPOSER_CAFILE

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
