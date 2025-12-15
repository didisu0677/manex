FROM 10.3.102.239:5050/devops/repo-infra-development:base-7.4

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . /var/www/html

# Remove docker files not needed in production
RUN rm -rf docker docker-compose.yml

# Create necessary directories FIRST
RUN mkdir -p /var/www/html/assets/cache \
    /var/www/html/assets/uploads/setting \
    /var/www/html/assets/uploads/temp \
    /var/www/html/application/cache/session

# Set basic ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Set specific write permissions for cache and upload directories AFTER basic permissions
RUN chmod -R 775 /var/www/html/assets/cache \
    && chmod -R 775 /var/www/html/assets/uploads \
    && chmod -R 775 /var/www/html/application/cache \
    && chown -R www-data:www-data /var/www/html/assets/cache \
    && chown -R www-data:www-data /var/www/html/assets/uploads \
    && chown -R www-data:www-data /var/www/html/application/cache

# Verify permissions are correct (optional - remove in production)
RUN echo "=== PERMISSION CHECK ===" \
    && ls -la /var/www/html/assets/ \
    && echo "Cache folder:" \
    && ls -la /var/www/html/assets/cache/ \
    && echo "======================="

EXPOSE 9000
CMD ["php-fpm"]