#!/bin/bash
set -e

echo "Ensuring all cache and upload directories exist with correct permissions..."

# Handle volume mounts from docker-compose
# The docker-compose mounts to /manex subfolders, but app expects root folders

# Create directories in the mounted volumes
mkdir -p /var/www/html/assets/uploads/manex/temp \
         /var/www/html/assets/uploads/manex/dokumen_file \
         /var/www/html/assets/uploads/manex/import \
         /var/www/html/assets/uploads/manex/user \
         /var/www/html/assets/uploads/manex/setting \
         /var/www/html/assets/cache/manex

# Create symlinks from expected paths to actual volume paths
echo "Creating symlinks for volume mounts..."

# Handle uploads symlinks
for dir in temp dokumen_file import user setting; do
    TARGET="/var/www/html/assets/uploads/$dir"
    SOURCE="/var/www/html/assets/uploads/manex/$dir"
    
    if [ -d "$TARGET" ] && [ ! -L "$TARGET" ]; then
        echo "Removing directory $TARGET to create symlink..."
        rm -rf "$TARGET"
    fi
    
    if [ ! -e "$TARGET" ]; then
        ln -sf "$SOURCE" "$TARGET"
        echo "Created symlink: $TARGET -> $SOURCE"
    fi
done

# Handle cache symlink - only if cache/manex exists (mounted volume)
if [ -d "/var/www/html/assets/cache/manex" ]; then
    echo "Cache volume detected, creating symlink..."
    # Remove existing cache directory if it's not a symlink
    if [ -d "/var/www/html/assets/cache" ] && [ ! -L "/var/www/html/assets/cache" ]; then
        echo "Backing up existing cache..."
        mv /var/www/html/assets/cache /var/www/html/assets/cache.bak 2>/dev/null || true
    fi
    
    if [ ! -e "/var/www/html/assets/cache" ]; then
        ln -sf /var/www/html/assets/cache/manex /var/www/html/assets/cache
        echo "Created symlink: /var/www/html/assets/cache -> /var/www/html/assets/cache/manex"
    fi
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
chown -R www-data:www-data /var/www/html/assets 2>/dev/null || true
chown -R www-data:www-data /var/www/html/application/cache 2>/dev/null || true
chown -R www-data:www-data /var/www/html/application/logs 2>/dev/null || true

# Set correct permissions
echo "Setting permissions..."
find /var/www/html/assets/uploads -type d -exec chmod 775 {} \; 2>/dev/null || true
find /var/www/html/assets/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
chmod -R 775 /var/www/html/assets/manex 2>/dev/null || true
chmod -R 775 /var/www/html/assets/json 2>/dev/null || true
chmod -R 775 /var/www/html/assets/qrcode 2>/dev/null || true
chmod -R 775 /var/www/html/application/cache 2>/dev/null || true
chmod -R 775 /var/www/html/application/logs 2>/dev/null || true

echo "All directories ready!"
ls -la /var/www/html/assets/uploads/ || true
ls -la /var/www/html/assets/ | grep cache || true

# Execute the main command (php-fpm)
exec "$@"
