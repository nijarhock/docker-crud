# Use the official PHP 7.2 image.
# https://hub.docker.com/_/php
FROM php:7.2-apache

# Copy local code to the container image.
COPY index.php /var/www/html/
COPY conn.php /var/www/html/
COPY main_function.php /var/www/html/
COPY axtambah.php /var/www/html/
COPY composer.json /var/www/html/

#Install git
RUN apt-get update \
    && apt-get install -y git
RUN docker-php-ext-install pdo pdo_mysql mysqli php-mysqli
RUN a2enmod rewrite
#Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=. --filename=composer
RUN mv composer /usr/local/bin/

# Use port 8080 in Apache configuration files.
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Configure PHP for development.
# Switch to the production php.ini for production operations.
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# https://hub.docker.com/_/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
