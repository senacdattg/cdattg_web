FROM php:8.3-fpm

# Actualizar e instalar dependencias de sistema (Debian/Ubuntu)
RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
       git curl bash supervisor unzip zip \
       libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
       libjpeg-dev libfreetype6-dev libwebp-dev \
       pkg-config ca-certificates \
       nodejs npm \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP (gd con jpeg/freetype/webp)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get update -y && apt-get install -y --no-install-recommends $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorios necesarios
RUN mkdir -p /var/www/html \
    && mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/www/html /var/log/supervisor

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar script de inicialización
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copiar configuración de supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Exponer puertos
EXPOSE 9000 8080

# Usar script de inicialización
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
