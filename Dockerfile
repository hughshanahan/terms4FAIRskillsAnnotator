FROM php:8.1-apache

# Install PHP MYSQL extension
RUN docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
# Configure Apache to allow rewriting
    && a2enmod rewrite \
# Install Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# copy the apache config file to the apache directory
COPY ./docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# copy the source to the container
COPY . /var/www/
