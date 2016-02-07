FROM php:7-apache

# Install PHP extensions required by Lumen (framework)
RUN apt-get update && \
    apt-get install -y libmcrypt-dev zlib1g-dev && \
    docker-php-ext-install pdo pdo_mysql mcrypt mbstring zip && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

# Add and enable VirtualHost definition
COPY build/000-default.conf /etc/apache2/sites-available/
RUN a2enmod ssl && a2ensite 000-default

# Override default Apache2 runtime conf
COPY build/apache2.conf /etc/apache2/

# Bundle the source code inside the image
COPY ./ /var/www

RUN composer install -vvv && \
    chown -R www-data:www-data storage