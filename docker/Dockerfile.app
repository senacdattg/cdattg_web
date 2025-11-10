# =========================================
# === Stage 1: Composer (dependencias PHP)
# =========================================
FROM php:8.3-cli AS composer_vendor

ARG BUILD_ENV=production
WORKDIR /var/www/html

# --- Dependencias del sistema ---
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git unzip zip \
        libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
        libxml2-dev libzip-dev libonig-dev; \
    apt-get install -y --no-install-recommends $PHPIZE_DEPS; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" gd intl mbstring zip bcmath exif pcntl; \
    apt-get purge -y --auto-remove $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists/*

# --- Composer global ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Archivos base ---
COPY composer.json composer.lock ./
COPY app/Helpers/helper.php ./app/Helpers/helper.php

ENV COMPOSER_PROCESS_TIMEOUT=0

# --- Instalación de dependencias ---
RUN set -eux; \
    if [ "$BUILD_ENV" = "production" ]; then \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts --no-progress; \
        composer dump-autoload --optimize; \
    else \
        composer install --no-interaction --prefer-dist --no-scripts --no-progress; \
    fi


# =========================================
# === Stage 2: Node / Vite (assets)
# =========================================
FROM node:20-bullseye-slim AS assets_builder

ARG NODE_ENV=production
ENV NODE_ENV=$NODE_ENV

WORKDIR /app

# --- Dependencias Node ---
COPY package.json package-lock.json ./

RUN set -eux; \
    if [ -f package-lock.json ] && [ -s package-lock.json ]; then \
        npm ci || (echo "⚠️ npm ci falló, usando npm install como fallback..."; npm install); \
    else \
        echo "⚠️ package-lock.json no encontrado o vacío, usando npm install..."; \
        npm install; \
    fi;

# --- Copiar código fuente para build ---
COPY resources ./resources
COPY vite.config.js ./vite.config.js
COPY public ./public

# --- Compilar assets de producción ---
RUN npm run build && npm prune --omit=dev


# =========================================
# === Stage 3: Runtime (PHP-FPM final)
# =========================================
FROM php:8.3-fpm AS app_runtime

ARG BUILD_ENV=production
ARG APP_ENV=production
ARG APP_DEBUG=false

ENV APP_ENV=${APP_ENV}
ENV APP_DEBUG=${APP_DEBUG}
ENV BUILD_ENV=${BUILD_ENV}

WORKDIR /var/www/html

# --- Dependencias del sistema ---
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git curl bash supervisor unzip zip \
        libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
        libxml2-dev libzip-dev libonig-dev; \
    apt-get install -y --no-install-recommends $PHPIZE_DEPS; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath gd zip intl; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    apt-get purge -y --auto-remove $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists/*

# --- Copiar Composer desde stage anterior ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Copiar aplicación ---
COPY . .

# --- Copiar vendor y assets construidos ---
COPY --chown=www-data:www-data --from=composer_vendor /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data --from=assets_builder /app/public/build ./public/build

# --- Configuración del sistema ---
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/conf.d/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN set -eux; \
    chmod +x /usr/local/bin/entrypoint.sh; \
    mkdir -p storage bootstrap/cache /var/log/supervisor; \
    chown -R www-data:www-data storage bootstrap/cache /var/log/supervisor

# --- Cacheo condicional de Laravel ---
RUN set -eux; \
    if [ "$BUILD_ENV" = "production" ] && [ -f ".env" ]; then \
        php artisan config:cache && php artisan route:cache && php artisan view:cache; \
    else \
        echo "Skipping artisan caches during build (BUILD_ENV=${BUILD_ENV})"; \
    fi

# --- Entrypoint ---
ENV PATH="/var/www/html/vendor/bin:${PATH}"
EXPOSE 9000 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
