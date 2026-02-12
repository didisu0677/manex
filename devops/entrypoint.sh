#!/bin/bash
set -e

echo "Ensuring all cache and upload directories exist with correct permissions..."

# Handle volume mounts from docker-compose
# The docker-compose mounts to /manex subfolders, but app expects root folders
# Create necessary directories in mounted volumes
if [ -d "/var/www/html/assets/uploads/manex" ]; then
    echo "Detected volume mount to /manex subfolder, creating directories in mounted volume..."
    mkdir -p /var/www/html/assets/uploads/manex/temp \
             /var/www/html/assets/uploads/manex/dokumen_file \
             /var/www/html/assets/uploads/manex/import \
             /var/www/html/assets/uploads/manex/user \
             /var/www/html/assets/uploads/manex/setting
    
    # Create symlinks from expected paths to actual volume paths for uploads
    for dir in temp dokumen_file import user setting; do
        if [ ! -e "/var/www/html/assets/uploads/$dir" ]; then
            ln -sf /var/www/html/assets/uploads/manex/$dir /var/www/html/assets/uploads/$dir
            echo "Created symlink: /var/www/html/assets/uploads/$dir -> /var/www/html/assets/uploads/manex/$dir"
        fi
    done
else
    # No volume mount, create directories normally
    mkdir -p /var/www/html/assets/uploads/temp \
             /var/www/html/assets/uploads/dokumen_file \
             /var/www/html/assets/uploads/import \
             /var/www/html/assets/uploads/user \
             /var/www/html/assets/uploads/setting
fi

# Handle cache mount
if [ -d "/var/www/html/assets/cache/manex" ]; then
    echo "Using cache from mounted volume..."
    # If cache is mounted to /manex subfolder, create symlink
    if [ ! -L "/var/www/html/assets/cache" ] && [ -d "/var/www/html/assets/cache" ]; then
        # Remove directory and create symlink
        rm -rf /var/www/html/assets/cache
    fi
    if [ ! -e "/var/www/html/assets/cache" ]; then
        ln -sf /var/www/html/assets/cache/manex /var/www/html/assets/cache
        echo "Created symlink: /var/www/html/assets/cache -> /var/www/html/assets/cache/manex"
    fi
else
    mkdir -p /var/www/html/assets/cache
fi

# Create other necessary directories
mkdir -p /var/www/html/application/cache/session \
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
                           /var/www/html/application/logs 2>/dev/null || true

# Set correct permissions (775 = rwxrwxr-x - owner, group can write, others can read/execute)
echo "Setting permissions..."
chmod -R 775 /var/www/html/assets/uploads \
             /var/www/html/assets/cache \
             /var/www/html/assets/manex \
             /var/www/html/assets/json \
             /var/www/html/assets/qrcode \
             /var/www/html/application/cache \
             /var/www/html/application/logs 2>/dev/null || true

echo "All directories ready!"

# Execute the main command (php-fpm)
exec "$@"
