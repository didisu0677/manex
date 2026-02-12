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
         /var/www/html/assets/cache/manex \
         /var/www/html/assets/manex/uploads

# Create symlinks from expected paths to actual volume paths
echo "Creating symlinks for volume mounts..."

# Handle uploads symlinks for application use
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

# IMPORTANT: Create symlink in manex-assets volume for nginx access
# Nginx mounts manex-assets at /var/www/html/manex/assets/
# So nginx will access: /var/www/html/manex/assets/uploads/
# This needs to point to the actual upload location
echo "Creating uploads symlink for nginx access..."
NGINX_UPLOADS_LINK="/var/www/html/assets/manex/uploads"
NGINX_UPLOADS_SOURCE="/var/www/html/assets/uploads/manex"

if [ -d "$NGINX_UPLOADS_LINK" ] && [ ! -L "$NGINX_UPLOADS_LINK" ]; then
    rm -rf "$NGINX_UPLOADS_LINK"
fi

if [ ! -e "$NGINX_UPLOADS_LINK" ]; then
    ln -sf "$NGINX_UPLOADS_SOURCE" "$NGINX_UPLOADS_LINK"
    echo "Created nginx uploads symlink: $NGINX_UPLOADS_LINK -> $NGINX_UPLOADS_SOURCE"
fi

# Handle cache symlink - only if cache/manex exists (mounted volume)
if [ -d "/var/www/html/assets/cache/manex" ]; then
    echo "Cache volume detected at /assets/cache/manex"
    
    # Copy existing cache files to mounted volume before creating symlink
    if [ -d "/var/www/html/assets/cache" ] && [ ! -L "/var/www/html/assets/cache" ]; then
        echo "Migrating existing cache files to mounted volume..."
        find /var/www/html/assets/cache -maxdepth 1 -type f \( -name "*.js" -o -name "*.css" \) -exec cp -p {} /var/www/html/assets/cache/manex/ \; 2>/dev/null || true
        
        # Remove original cache directory
        echo "Removing original cache directory..."
        rm -rf /var/www/html/assets/cache
    fi
    
    # Create symlink from expected cache path to mounted volume
    if [ ! -e "/var/www/html/assets/cache" ]; then
        ln -sf /var/www/html/assets/cache/manex /var/www/html/assets/cache
        echo "Created cache symlink: /var/www/html/assets/cache -> /var/www/html/assets/cache/manex"
    fi
    
    # Verify symlink
    if [ -L "/var/www/html/assets/cache" ]; then
        echo "Cache symlink verified OK"
        ls -la /var/www/html/assets/cache | head -5
    fi
else
    echo "No cache volume mount detected, using local directory"
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
