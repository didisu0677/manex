FROM 10.3.102.239:5050/devops/repo-infra-development:base-7.4

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . /var/www/html

# Remove docker files not needed in production
RUN rm -rf docker docker-compose.yml

# Permissions fix
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && mkdir -p /var/www/html/assets/cache /var/www/html/assets/uploads \
    && chmod -R 775 /var/www/html/assets \
    && chown -R www-data:www-data /var/www/html/assets
# Session folder fix
RUN mkdir -p /var/www/html/storage/sessions \
    && chown -R www-data:www-data /var/www/html/storage/sessions \
    && chmod -R 775 /var/www/html/storage/sessions
# php.ini
# COPY devops/php/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000
CMD ["php-fpm"]