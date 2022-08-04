FROM php:8.1-apache

RUN \
# Update
    apt-get update -y \
# install Libzip
    && apt-get install libzip-dev -y  \
# Install PHP ZIP extension
    && docker-php-ext-install zip && docker-php-ext-enable zip \
# Install PHP MYSQL extension
    && docker-php-ext-install mysqli && docker-php-ext-enable mysqli \
# Configure Apache to allow rewriting
    && a2enmod rewrite \
# Install Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# copy the apache config file to the apache directory
COPY ./docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# copy the source to the container
COPY . /var/www/

# Set working directory
WORKDIR /var/www/

# Install the Composer dependencies
RUN composer install