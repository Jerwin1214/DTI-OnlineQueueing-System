FROM php:8.4-apache

# ================================
# Install System Dependencies
# ================================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# ================================
# Enable Apache Rewrite
# ================================
RUN a2enmod rewrite

# ================================
# Install Composer
# ================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ================================
# Set Working Directory
# ================================
WORKDIR /var/www/html

# ================================
# Copy Project Files
# ================================
COPY . .

# ================================
# Set Apache to Laravel /public
# ================================
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# ================================
# Install Laravel Dependencies (Production)
# ================================
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ================================
# Create Storage Link
# ================================
RUN php artisan storage:link

# ================================
# Fix Permissions
# ================================
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# ================================
# Change Apache Port for Render
# ================================
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:10000/g' /etc/apache2/sites-available/000-default.conf

# ================================
# Expose Render Port
# ================================
EXPOSE 10000

# ================================
# Start Apache
# ================================
CMD ["apache2-foreground"]
