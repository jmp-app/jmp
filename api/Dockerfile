FROM php:7-fpm-alpine as builder

WORKDIR /var/www

# Install dependencies
#RUN apt-get update && apt-get install -y \
#    build-essential \
#    zip \
#    unzip \
#    git \
#    curl
#
# Clear cache
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY src src
COPY composer.json composer.json

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer self-update

RUN composer install --classmap-authoritative

FROM php:7-fpm-alpine

WORKDIR /var/www

# Install extensions
RUN docker-php-ext-install pdo_mysql

COPY --from=builder /var/www .
COPY cache cache
#COPY log log
COPY public/index.php public/index.php

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
