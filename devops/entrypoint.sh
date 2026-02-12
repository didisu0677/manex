#!/bin/bash
set -e

echo "Ensuring all cache and upload directories exist with correct permissions..."

# Create upload directories
mkdir -p /var/www/html/assets/uploads/temp \
         /var/www/html/assets/uploads/dokumen_file \
         /var/www/html/assets/uploads/import \
         /var/www/html/assets/uploads/user \
         /var/www/html/assets/uploads/setting

# Create cache directories
mkdir -p /var/www/html/application/cache/session \
         /var/www/html/assets/cache \
         /var/www/html/assets/manex/cache \
         /var/www/html/assets/manex/uploads \
         /var/www/html/assets/manex/writable \
         /var/www/html/assets/json \
         /var/www/html/assets/qrcode \
         /var/www/html/application/logs

# Set correct ownership
echo "Setting ownership to www-data..."
chown -R www-data:www-data /var/www/html/assets/uploads \
                           /var/www/html/assets/cache \
                           /var/www/html/assets/manex \
                           /var/www/html/assets/json \
                           /var/www/html/assets/qrcode \
                           /var/www/html/application/cache \
                           /var/www/html/application/logs

# Set correct permissions (775 = rwxrwxr-x - owner, group can write, others can read/execute)
echo "Setting permissions..."
chmod -R 775 /var/www/html/assets/uploads \
             /var/www/html/assets/cache \
             /var/www/html/assets/manex \
             /var/www/html/assets/json \
             /var/www/html/assets/qrcode \
             /var/www/html/application/cache \
             /var/www/html/application/logs

echo "All directories ready!"

# Execute the main command (php-fpm)
exec "$@"
