FROM composer:latest as builder

WORKDIR /var/www

COPY src src
COPY composer.json composer.json

RUN composer install --classmap-authoritative --no-suggest --no-dev --no-progress --no-interaction

FROM php:7.3-apache-stretch

WORKDIR /var/www/html

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=builder /var/www .
COPY cache cache
COPY public/* public/*
COPY .htaccess .

RUN chmod a+w cache -R