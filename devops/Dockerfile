FROM 10.3.102.239:5050/devops/repo-infra-development:base-7.4

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . /var/www/html

RUN mkdir -p /var/www/html/application/cache \
    /var/www/html/application/logs \
    /var/www/html/assets/cache \
    /var/www/html/assets/uploads

RUN chown -R www-data:www-data /var/www/html

RUN find /var/www/html/application -type d -exec chmod 755 {} \; \
    && find /var/www/html/application -type f -exec chmod 644 {} \;

RUN chmod 555 /var/www/html/application/admin

RUN chmod 775 /var/www/html/application/cache \
    && chmod 775 /var/www/html/application/logs \
    && chmod 775 /var/www/html/assets/cache \
    && chmod 775 /var/www/html/assets/uploads

RUN find /var/www/html/assets -type d -exec chmod 755 {} \; \
    && find /var/www/html/assets -type f -exec chmod 644 {} \;

RUN chmod -R 555 /var/www/html/system

EXPOSE 9000
CMD ["php-fpm"]