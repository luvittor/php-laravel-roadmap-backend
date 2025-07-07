# Use official PHP 8.2 FPM image as a base
FROM php:8.2-fpm

# -------------------------------------------------------------
# Install system dependencies needed for Laravel and common PHP extensions
# -------------------------------------------------------------
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    iproute2 \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------------------------------------
# Install PHP extensions required by Laravel
# -------------------------------------------------------------
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip

# -------------------------------------------------------------
# Enable OPcache with a basic development-friendly configuration
# -------------------------------------------------------------
RUN { \
    echo "opcache.memory_consumption=128"; \
    echo "opcache.interned_strings_buffer=8"; \
    echo "opcache.max_accelerated_files=10000"; \
    # check timestamps every 1 second
    echo "opcache.revalidate_freq=1"; \ 
    # allow auto-recompile when files change
    echo "opcache.validate_timestamps=1"; \ 
    # turn OPcache on
    echo "opcache.enable=1"; \ 
    # optional: enable for CLI too
    echo "opcache.enable_cli=1"; \ 
  } > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# -------------------------------------------------------------
# Install Composer (latest) by copying from official Composer image
# -------------------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -------------------------------------------------------------
# Set working directory inside the container
# -------------------------------------------------------------
WORKDIR /var/www/html

# -------------------------------------------------------------
# Copy application code into container.
# Note: docker-compose will mount your host folder on top of /var/www/html at runtime,
# so this COPY mainly optimizes build caching.
# -------------------------------------------------------------
COPY . /var/www/html

# -------------------------------------------------------------
# Ensure storage and cache are writable by www-data user
# -------------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# -------------------------------------------------------------
# Expose port 9000 so PHP-FPM listens on it (nginx will proxy here)
# -------------------------------------------------------------
EXPOSE 9000

# -------------------------------------------------------------
# Default command: start PHP-FPM
# -------------------------------------------------------------
CMD ["php-fpm"]
