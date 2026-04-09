FROM php:8.2-apache

# 1. Install system dependencies & PHP extensions
# We add mysqli and bcmath for your C2C platform's flexibility
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo_mysql mysqli bcmath

# 2. Enable Apache mod_rewrite (Essential for clean URLs)
RUN a2enmod rewrite

# 3. Set document root to public/ (The 'Claude' way, but more robust)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -i "s|/var/www/html|$APACHE_DOCUMENT_ROOT|g" /etc/apache2/sites-available/000-default.conf \
    && sed -i "s|/var/www/html|$APACHE_DOCUMENT_ROOT|g" /etc/apache2/apache2.conf

# 4. Copy code (Use a .dockerignore file to skip the heavy stuff!)
COPY . /var/www/html

# 5. Set permissions so Apache can actually write to your src (for logs/uploads)
RUN chown -R www-data:www-data /var/www/html
