FROM php:8.1-apache

# Install zip
RUN apt-get update
RUN apt-get install -y libzip-dev 
RUN docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
