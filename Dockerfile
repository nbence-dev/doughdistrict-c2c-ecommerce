FROM php:8.2-apache

# 1. Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    && docker-php-ext-install pdo_mysql mysqli bcmath

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Enable Apache mod_rewrite (Essential for clean URLs)
RUN a2enmod rewrite

# 4. Set document root to public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -i "s|/var/www/html|$APACHE_DOCUMENT_ROOT|g" /etc/apache2/sites-available/000-default.conf \
    && sed -i "s|/var/www/html|$APACHE_DOCUMENT_ROOT|g" /etc/apache2/apache2.conf

# 5. Copy code (vendor/ excluded via .dockerignore)
COPY . /var/www/html

# 6. Install PHP dependencies
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# 7. Set permissions so Apache can read everything
RUN chown -R www-data:www-data /var/www/html
