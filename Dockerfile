FROM php:8.1-apache

# copy the apache config file to the apache directory
COPY ./docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# copy the source to the container
COPY ./ /var/www/

# Set working directory
WORKDIR /var/www/

RUN \
# Update
    apt-get update -y \
# install Libzip
    && apt-get install libzip-dev -y  \
# Install Wget
    && apt-get install wget -y \
# Install PHP ZIP extension
    && docker-php-ext-install zip && docker-php-ext-enable zip \
# Install PHP MYSQL extension
    && docker-php-ext-install mysqli && docker-php-ext-enable mysqli \
# Configure Apache to allow rewriting
    && a2enmod rewrite \
# Install Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
# Install PHPDoc
    && wget https://phpdoc.org/phpDocumentor.phar \
    && chmod +x phpDocumentor.phar \
    && mv phpDocumentor.phar /usr/local/bin/phpDocumentor \
# Install JSDoc
    && apt-get install jsdoc-toolkit -y

ENTRYPOINT ["sh", "start.sh"]
CMD ["run"]