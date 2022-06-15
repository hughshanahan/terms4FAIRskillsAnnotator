FROM php:8.1-apache

# Configure Apache
RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY . /var/www
RUN service apache2 restart

# set the working directory to be /var/www
WORKDIR /var/www

# Install zip
RUN apt-get update
RUN apt-get install -y libzip-dev 
RUN docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
