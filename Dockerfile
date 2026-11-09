# Production image for Hotel Management System.
# Targets: `app` (PHP-FPM) and `nginx` (static + FastCGI to app).
# No Node process runs in the final images.

FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public
COPY . .
RUN npm run build

# composer:2 currently ships PHP 8.5; lockfile packages target ≤8.4.
# Ignore platform here — the final app image is php:8.3-fpm with required extensions.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && mkdir -p bootstrap/cache \
    && cp .env.example .env \
    && php -r "file_put_contents('.env', preg_replace('/^APP_KEY=.*/m', 'APP_KEY=base64:'.base64_encode(random_bytes(32)), file_get_contents('.env')));" \
    && php artisan package:discover --ansi \
    && rm -f .env

FROM php:8.3-fpm-bookworm AS app

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libzip-dev libicu-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath exif gd intl mbstring opcache pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/app-entrypoint.sh /usr/local/bin/app-entrypoint.sh
RUN chmod +x /usr/local/bin/app-entrypoint.sh \
    && sed -i 's/\r$//' /usr/local/bin/app-entrypoint.sh

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

ENTRYPOINT ["/usr/local/bin/app-entrypoint.sh"]
CMD ["php-fpm"]

FROM nginx:1.27-alpine AS nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
