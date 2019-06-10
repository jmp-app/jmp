FROM composer:latest as builder

WORKDIR /var/www

COPY api/src src
COPY api/composer.json composer.json

RUN composer install --classmap-authoritative --no-suggest --no-dev --no-progress --no-interaction

FROM php:7.3-apache-stretch

WORKDIR /var/www/html

# Install extensions
RUN docker-php-ext-install pdo_mysql

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=builder /var/www .
COPY api/cache cache
COPY api/public/* public/
COPY api/.htaccess .
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data . \
    && a2enmod rewrite

# From https://stackoverflow.com/a/54469924
ENTRYPOINT []
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-enabled/000-default.conf /etc/apache2/ports.conf && docker-php-entrypoint apache2-foreground
