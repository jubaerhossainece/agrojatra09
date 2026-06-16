# ─────────────────────────────────────────────
# Stage 1: Clone source from GitHub
# ─────────────────────────────────────────────
FROM alpine/git AS source

# Pass the latest git commit SHA at build time to bust the clone cache.
# Usage: docker compose build --build-arg GIT_SHA=$(git rev-parse HEAD)
# Without this arg, Docker reuses the cached clone and never picks up new commits.
ARG GIT_SHA=unknown
RUN echo "Building from commit: $GIT_SHA"

WORKDIR /src
RUN git clone https://github.com/jubaerhossainece/agrojatra09.git .

# ─────────────────────────────────────────────
# Stage 2: Node – build frontend assets (Vite)
# ─────────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY --from=source /src/package.json /src/package-lock.json ./
RUN npm install --legacy-peer-deps

COPY --from=source /src/resources/ resources/
COPY --from=source /src/vite.config.js /src/tailwind.config.js /src/postcss.config.js ./
COPY --from=source /src/public/ public/

RUN npm run build

# ─────────────────────────────────────────────
# Stage 3: PHP – install Composer dependencies
# ─────────────────────────────────────────────
FROM composer:2 AS composer-builder

WORKDIR /app

COPY --from=source /src/composer.json /src/composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY --from=source /src .
RUN composer dump-autoload --optimize --ignore-platform-reqs

# ─────────────────────────────────────────────
# Stage 4: Final runtime image
# ─────────────────────────────────────────────
FROM php:8.3-fpm-alpine

LABEL maintainer="agrojatra09"

# Install system libraries + PHP extensions
# pdo_mysql replaces pdo_sqlite — DB runs on the host machine, not in Docker
RUN apk add --no-cache \
        nginx \
        supervisor \
        netcat-openbsd \
        libzip-dev \
        zip \
        unzip \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        libxml2-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        xml \
        opcache \
        bcmath \
        pcntl

# PHP-FPM: ensure it listens on TCP 9000 (default), allow from nginx
RUN sed -i \
        -e 's|^listen = .*|listen = 127.0.0.1:9000|' \
        -e 's|;listen.allowed_clients|listen.allowed_clients|' \
        /usr/local/etc/php-fpm.d/www.conf

# Config files
COPY conf/nginx.conf       /etc/nginx/nginx.conf
COPY conf/supervisord.conf /etc/supervisord.conf
COPY conf/opcache.ini      /usr/local/etc/php/conf.d/opcache.ini
COPY entrypoint.sh         /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html

# Copy built application
COPY --from=composer-builder /app .
COPY --from=node-builder /app/public/build public/build

# Bootstrap .env from example — real secrets come in via docker-compose environment
COPY --from=source /src/.env.example .env

# Storage dirs + permissions
# NOTE: No SQLite setup here — MySQL lives on the host, not in the image
RUN mkdir -p \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/logs \
        storage/app/public \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Generate a stable app key baked into the image layer
RUN php artisan key:generate --force

EXPOSE 8080

# entrypoint.sh waits for host MySQL, runs migrations, then starts supervisord
ENTRYPOINT ["/entrypoint.sh"]
