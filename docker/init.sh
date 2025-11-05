#!/bin/bash

echo "🚀 Inicializando aplicación CDATTG..."

# Verificar que Docker está corriendo
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker no está corriendo. Por favor, inicia Docker primero."
    exit 1
fi

# Construir imágenes (solo si es necesario)
echo "📦 Construyendo imágenes Docker..."
docker-compose build --no-cache app

# Iniciar servicios base
echo "⏳ Iniciando servicios base (DB, Redis)..."
docker-compose up -d db redis

# Esperar a que MySQL esté listo
echo "⏳ Esperando a que MySQL esté listo..."
sleep 5
until docker-compose exec -T db mysqladmin ping -h localhost --silent 2>/dev/null; do
    echo "   Esperando MySQL..."
    sleep 2
done
echo "✅ MySQL está listo"

# Iniciar aplicación
echo "▶️ Iniciando aplicación..."
docker-compose up -d app

# Esperar a que la app esté lista
echo "⏳ Esperando a que la aplicación esté lista..."
sleep 5

# Instalar dependencias dentro del contenedor si no están
echo "📦 Verificando dependencias..."
docker-compose exec -T app sh -c "
    if [ ! -d vendor ]; then
        echo 'Instalando dependencias PHP...'
        composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
    fi
    if [ ! -d node_modules ]; then
        echo 'Instalando dependencias Node.js...'
        npm ci --prefer-offline --no-audit || npm install --prefer-offline --no-audit
    fi
    if [ ! -d public/build ] && [ ! -d public/dist ]; then
        echo 'Compilando assets...'
        npm run build
    fi
" || true

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
docker-compose exec -T app php artisan migrate:module --all --fresh || {
    echo "⚠️ Error ejecutando migraciones, intentando de nuevo..."
    sleep 2
    docker-compose exec -T app php artisan migrate:module --all --fresh
}

# Ejecutar seeders
echo "🌱 Ejecutando seeders..."
docker-compose exec -T app php artisan db:seed --force || true

# Configurar aplicación
echo "🔑 Configurando aplicación..."
docker-compose exec -T app php artisan key:generate --force || true
docker-compose exec -T app php artisan config:cache || true
docker-compose exec -T app php artisan route:cache || true
docker-compose exec -T app php artisan view:cache || true

# Configurar permisos
echo "🔐 Configurando permisos..."
docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
docker-compose exec -T app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Iniciar todos los servicios
echo "▶️ Iniciando todos los servicios..."
docker-compose up -d

echo ""
echo "✅ ¡Aplicación inicializada exitosamente!"
echo ""
echo "🌐 Aplicación web: http://localhost:8000"
echo "🔌 WebSocket (Reverb): ws://localhost:8080"
echo "📊 Playwright validator: http://localhost:3000"
echo ""
echo "📝 Comandos útiles:"
echo "   Ver logs: docker-compose logs -f app"
echo "   Acceder al contenedor: docker-compose exec app sh"
echo "   Detener servicios: docker-compose down"
echo "   Reiniciar: docker-compose restart"

