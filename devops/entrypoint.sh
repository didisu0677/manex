#!/bin/bash
set -e

# Ensure upload directories exist with correct permissions
echo "Creating upload directories..."
mkdir -p /var/www/html/assets/uploads/temp \
         /var/www/html/assets/uploads/dokumen_file \
         /var/www/html/assets/uploads/import \
         /var/www/html/assets/uploads/user \
         /var/www/html/assets/uploads/setting

# Set correct ownership and permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/assets/uploads
chmod -R 775 /var/www/html/assets/uploads

echo "Upload directories ready!"

# Execute the main command (php-fpm)
exec "$@"
