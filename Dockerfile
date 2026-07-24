# ==========================
# Stage 1 - Frontend Build
# ==========================
FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN npm run build


# ==========================
# Stage 2 - Composer
# ==========================
FROM composer:2 AS vendor

WORKDIR /app

COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts


# ==========================
# Stage 3 - Production
# ==========================
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    unzip \
    git \
    curl \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
        pdo_mysql \
        intl \
        mbstring \
        bcmath \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .


COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p storage/framework/cache \
    storage/framework/views \
    storage/framework/sessions

RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

RUN php artisan package:discover --ansi || true

RUN php artisan storage:link || true

RUN php artisan config:cache || true

RUN php artisan route:cache || true

RUN php artisan view:cache || true

COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh

RUN chmod +x /start.sh

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

EXPOSE 3000

CMD ["/start.sh"]