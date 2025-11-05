#!/bin/bash
set -e

echo "🚀 Iniciando contenedor de aplicación..."

# Cambiar al directorio de trabajo
cd /var/www/html

# Esperar a que la base de datos esté lista (si está en docker-compose)
if [ -n "$DB_HOST" ]; then
    echo "⏳ Esperando a que MySQL esté listo..."
    until php -r "try { new PDO('mysql:host=$DB_HOST;port=3306', '$DB_USERNAME', '$DB_PASSWORD'); exit(0); } catch (PDOException \$e) { exit(1); }" 2>/dev/null; do
        echo "Esperando conexión a MySQL..."
        sleep 2
    done
    echo "✅ MySQL está listo"
fi

# Instalar dependencias si no existen
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependencias PHP..."
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || {
        echo "⚠️ Error instalando dependencias PHP, intentando de nuevo..."
        composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts || true
    }
fi

if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias Node.js..."
    npm ci --prefer-offline --no-audit || npm install --prefer-offline --no-audit || true
fi

# Compilar assets si no existen
if [ ! -d "public/build" ] && [ ! -d "public/dist" ]; then
    echo "🔨 Compilando assets..."
    npm run build || true
fi

# Ejecutar scripts de Composer si no se ejecutaron
if [ ! -f "vendor/.composer-scripts-executed" ]; then
    echo "📋 Ejecutando scripts de Composer..."
    php artisan package:discover --ansi || true
    touch vendor/.composer-scripts-executed 2>/dev/null || true
fi

# Configurar permisos
echo "🔐 Configurando permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Generar clave de aplicación si no existe
if [ ! -f ".env" ] || ! grep -q "APP_KEY=" .env 2>/dev/null || grep -q "APP_KEY=$" .env 2>/dev/null; then
    echo "🔑 Generando clave de aplicación..."
    php artisan key:generate --force || true
fi

echo "✅ Inicialización completada"

# Ejecutar el comando pasado como argumento
exec "$@"

