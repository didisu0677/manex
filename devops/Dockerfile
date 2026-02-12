FROM 10.3.102.239:5050/devops/repo-infra-development:base-7.4

WORKDIR /var/www/html

# Install composer dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copy project files
COPY . /var/www/html

# Remove docker files not needed in production
RUN rm -rf docker docker-compose.yml

# Create all folders writable for PHP
RUN mkdir -p \
    /var/www/html/application/cache/session \
    /var/www/html/assets/cache \
    /var/www/html/assets/manex/cache \
    /var/www/html/assets/manex/uploads \
    /var/www/html/assets/manex/writable \
    /var/www/html/assets/uploads/temp \
    /var/www/html/assets/uploads/dokumen_file \
    /var/www/html/assets/uploads/import \
    /var/www/html/assets/uploads/user \
    /var/www/html/assets/uploads/setting

# Set ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 775 /var/www/html/application/cache \
    && chmod -R 775 /var/www/html/assets/cache \
    && chmod -R 775 /var/www/html/assets/manex \
    && chmod -R 775 /var/www/html/assets/uploads

# Expose PHP-FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
