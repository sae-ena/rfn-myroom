# Use an official PHP image with Apache
FROM php:8.0-apache

# Install required PHP extensions, including mysqli
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql mysqli && \
    apt-get clean

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Copy your PHP files into the Apache server's document root
COPY . /var/www/html/

# Set the working directory to /var/www/html (document root of Apache)
WORKDIR /var/www/html/

# Set file permissions for Apache to serve files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 to the outside world
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]