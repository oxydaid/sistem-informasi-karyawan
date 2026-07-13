# Stage 1: PHP Composer dependencies
FROM php:8.4-alpine AS composer-builder

WORKDIR /app

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install system dependencies needed for Composer build (if any)
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev

# Copy composer files
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Stage 2: Node.js assets builder
FROM node:22-alpine AS assets-builder

WORKDIR /app

# Install pnpm (since project uses pnpm-lock.yaml)
RUN npm install -g pnpm

# Copy package files
COPY package.json pnpm-lock.yaml ./

# Install packages
RUN pnpm install --frozen-lockfile

# Copy frontend source files
COPY resources/ ./resources/
COPY vite.config.js ./

# Build assets
RUN pnpm run build

# Stage 3: Production runtime environment
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    bash \
    icu-dev \
    oniguruma-dev

# Configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        pcntl \
        bcmath \
        opcache \
        intl \
        mbstring

# Install Redis extension (used for caching/queues)
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Copy system configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy application source
COPY . .

# Copy built composer vendors and assets from previous stages
COPY --from=composer-builder /app/vendor ./vendor
COPY --from=assets-builder /app/public/build ./public/build

# Adjust directories permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Configure ENTRYPOINT
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
