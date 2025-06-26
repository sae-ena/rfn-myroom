# Use an official PHP image with Apache
FROM php:8.0-apache

# Install required PHP extensions, including mysqli, and tools for Composer
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    git unzip zip libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip \
    && apt-get clean

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Copy composer files first for better Docker cache
COPY composer.json composer.lock /var/www/html/

# Install Composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Install PHP dependencies with Composer
RUN cd /var/www/html && composer install --no-interaction --no-dev --optimize-autoloader

# Now copy the rest of your app
COPY . /var/www/html/

# Set the working directory to /var/www/html (document root of Apache)
WORKDIR /var/www/html/

# Set file permissions for Apache to serve files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 to the outside world
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]