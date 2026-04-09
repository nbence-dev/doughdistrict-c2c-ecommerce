FROM php:8-apache

# Enable mod_rewrite for clean URLs
RUN a2enmod rewrite

# Install PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Set document root to public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/apache2.conf

COPY . /var/www/html
