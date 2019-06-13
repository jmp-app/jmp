FROM composer:latest as builder

WORKDIR /var/www

COPY composer.json composer.json

RUN composer install --classmap-authoritative --no-suggest --no-dev --no-progress --no-interaction

COPY src src

FROM php:7.3-fpm-alpine

WORKDIR /var/www

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=builder /var/www .
COPY cache cache
COPY public/index.php public/index.php

RUN chmod a+w cache -R

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
