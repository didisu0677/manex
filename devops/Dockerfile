FROM 10.3.102.239:5050/devops/repo-infra-development:base-7.4

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . /var/www/html

# Remove docker files not needed in production
RUN rm -rf docker docker-compose.yml

# Create necessary directories FIRST before any permission setting
RUN mkdir -p /var/www/html/assets/cache \
    /var/www/html/assets/uploads/setting \
    /var/www/html/assets/uploads/temp \
    /var/www/html/application/cache/session

# Set ownership to www-data FIRST
RUN chown -R www-data:www-data /var/www/html

# Set basic permissions for files and directories
RUN find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Set SPECIFIC write permissions for cache directories (AFTER basic permissions)
RUN chmod -R 775 /var/www/html/assets/cache \
    && chmod -R 775 /var/www/html/assets/uploads \
    && chmod -R 775 /var/www/html/application/cache

# Ensure www-data ownership for cache directories (FINAL step)
RUN chown -R www-data:www-data /var/www/html/assets/cache \
    && chown -R www-data:www-data /var/www/html/assets/uploads \
    && chown -R www-data:www-data /var/www/html/application/cache

# Create test file to verify write permission works
RUN echo "test" > /var/www/html/assets/cache/.dockertest \
    && rm -f /var/www/html/assets/cache/.dockertest \
    && echo "Cache write permission: OK"

EXPOSE 9000
CMD ["php-fpm"]