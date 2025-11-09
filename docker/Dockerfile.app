FROM php:8.3-cli AS composer_vendor

ARG BUILD_ENV=production
WORKDIR /var/www/html

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        libicu-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libwebp-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev; \
    apt-get install -y --no-install-recommends $PHPIZE_DEPS; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" gd intl mbstring zip bcmath exif pcntl; \
    apt-get purge -y --auto-remove $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
COPY app/Helpers/helper.php ./app/Helpers/helper.php

RUN set -eux; \
    if [ "$BUILD_ENV" = "production" ]; then \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts --no-progress; \
    else \
        composer install --no-interaction --prefer-dist --no-scripts --no-progress; \
    fi

FROM node:20-bullseye-slim AS assets_builder

ARG NODE_ENV=production
ENV BUILD_NODE_ENV=${NODE_ENV}
ENV NODE_ENV=development
WORKDIR /app

COPY package.json package-lock.json ./

RUN set -eux; \
    echo "📦 Verificando archivos copiados..."; \
    ls -lh package*.json || true; \
    if [ -f package-lock.json ] && [ -s package-lock.json ]; then \
        echo "✅ package-lock.json encontrado, usando npm ci..."; \
        npm ci || (echo "⚠️ npm ci falló, usando npm install como fallback..."; npm install); \
    else \
        echo "⚠️ package-lock.json no encontrado o vacío, usando npm install..."; \
        npm install; \
    fi;

COPY resources ./resources
COPY vite.config.js ./vite.config.js
COPY public ./public

RUN set -eux; \
    npm run build

RUN set -eux; \
    npm prune --omit=dev

ENV NODE_ENV=${BUILD_NODE_ENV}

FROM php:8.3-fpm AS app_runtime

ARG BUILD_ENV=production
ARG APP_ENV=production
ARG APP_DEBUG=false
ENV APP_ENV=${APP_ENV}
ENV APP_DEBUG=${APP_DEBUG}
ENV BUILD_ENV=${BUILD_ENV}
WORKDIR /var/www/html

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        curl \
        bash \
        supervisor \
        unzip \
        zip \
        libicu-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libwebp-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev; \
    apt-get install -y --no-install-recommends $PHPIZE_DEPS; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath gd zip intl; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    apt-get purge -y --auto-remove $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists/*

COPY . .

RUN set -eux; \
    mkdir -p /var/log/supervisor; \
    chmod +x docker/entrypoint.sh; \
    chown -R www-data:www-data storage bootstrap/cache /var/log/supervisor || true

COPY --from=composer_vendor /var/www/html/vendor ./vendor
COPY --from=assets_builder /app/public/build ./public/build

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY --from=composer_vendor /usr/bin/composer /usr/bin/composer
COPY docker/php/conf.d/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

RUN chmod +x /usr/local/bin/entrypoint.sh

ENV PATH="/var/www/html/vendor/bin:${PATH}"

EXPOSE 9000 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
