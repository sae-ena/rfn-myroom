# Use an official PHP image with Apache
FROM php:8.0-apache

# Install required packages and PHP extensions

    RUN apt-get update && \
    apt-get install -y \
    libapache2-mod-php \
    php-mysql \
    && apt-get clean


# Enable Apache mod_rewrite (if needed for routing or pretty URLs)
RUN a2enmod rewrite

# # Copy your PHP project files into the container
# COPY ./src /var/www/html/

# # Set permissions for Apache
# RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for web traffic
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
