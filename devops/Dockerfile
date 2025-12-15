FROM 10.3.102.239:5050/devops/repo-infra-development:base-7.4

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . /var/www/html

# Remove docker files not needed in production
RUN rm -rf docker docker-compose.yml

# Set CI Environment untuk development
ENV CI_ENV=development

# Create directories and set permissions in one step
RUN mkdir -p /var/www/html/assets/cache \
    /var/www/html/assets/uploads/setting \
    /var/www/html/assets/uploads/temp \
    /var/www/html/application/cache/session \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 777 /var/www/html/assets/cache \
    && chmod -R 777 /var/www/html/assets/uploads \
    && chmod -R 777 /var/www/html/application/cache \
    && echo "test cache" > /var/www/html/assets/cache/.dockertest \
    && rm -f /var/www/html/assets/cache/.dockertest \
    && echo "Cache write test: SUCCESS"
# php.ini
# COPY devops/php/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000
CMD ["php-fpm"]