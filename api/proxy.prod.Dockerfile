FROM composer:latest as builder

WORKDIR /var/www

COPY src src
COPY composer.json composer.json

# Copy certificates and set environment variable
COPY Dockerfile certs.pem* ./
RUN if [[ -f /var/www/certs.pem ]]; then \
        export COMPOSER_CAFILE=/var/www/certs.pem; \
    fi;
ENV COMPOSER_CAFILE $COMPOSER_CAFILE

RUN composer install --classmap-authoritative --no-suggest --no-dev --no-progress --no-interaction

FROM php:7.3-fpm-alpine

WORKDIR /var/www

# Install extensions
RUN docker-php-ext-install pdo_mysql

COPY --from=builder /var/www .
COPY cache cache
COPY public/index.php public/index.php

RUN chmod a+w cache -R

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]