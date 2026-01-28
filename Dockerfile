# Use official PHP with Apache
FROM php:8.2-apache

# Enable Apache rewrite module (very important for PHP frameworks)
RUN a2enmod rewrite

# Install common PHP extensions (add more if needed)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy project files into Apache root
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80
