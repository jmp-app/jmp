FROM php:7.2-fpm

WORKDIR /var/www

# install curl and php
RUN apt-get update -y
RUN apt-get install curl git zip -y

# call PHP images script `docker-php-ext-install` and install language extensions
RUN docker-php-ext-install pdo pdo_mysql

# install composer & app deps; then remove
# RUN curl -sS https://getcomposer.org/installer | php
# RUN mv composer.phar /usr/local/bin/composer

# Copy existing application directory contents
COPY api/ /var/www/

# Install dependecies form composer
# RUN composer install

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]