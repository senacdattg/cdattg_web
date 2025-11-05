#!/bin/bash

echo "🚀 Iniciando construcción de contenedores..."

# Construir imágenes
echo "📦 Construyendo imágenes Docker..."
docker-compose build

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
docker-compose run --rm app php artisan migrate --force

# Ejecutar seeders (opcional - comentado para evitar errores)
echo "🌱 Saltando seeders por ahora..."
# docker-compose run --rm app php artisan db:seed --force

# Limpiar cache
echo "🧹 Limpiando cache..."
docker-compose run --rm app php artisan config:cache
docker-compose run --rm app php artisan route:cache
docker-compose run --rm app php artisan view:cache

# Instalar dependencias de producción
echo "📦 Optimizando dependencias..."
docker-compose run --rm app composer install --no-dev --optimize-autoloader

# Iniciar servicios
echo "▶️ Iniciando servicios..."
docker-compose up -d

echo "✅ Despliegue completado exitosamente!"
echo "🌐 Aplicación disponible en: http://localhost:8000"
echo "📊 Playwright validator en: http://localhost:3000"