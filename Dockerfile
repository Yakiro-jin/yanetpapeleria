FROM php:8.2-apache

# Install PostgreSQL extensions and other dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql gd zip

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Copy custom Apache configuration for Laravel
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# **PERMISOS ESPECÍFICOS PARA LARAVEL:**
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/artisan

# Install Composer (opcional pero recomendado)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies (si quieres hacerlo en build)
# RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

CMD ["apache2-foreground"]
