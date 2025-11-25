FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    curl \
    gnupg2 \
    unixodbc-dev \
    libgss3 \
    libgssapi-krb5-2 \
    libcurl4-openssl-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite && a2enmod headers

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set workdir
WORKDIR /var/www/html

# Copy application source code
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --no-dev --prefer-dist || true

# Git safe directory
RUN git config --global --add safe.directory /var/www/html
