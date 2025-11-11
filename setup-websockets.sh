#!/bin/bash

# Script de configuración para WebSockets con Docker
# Este script ayuda a configurar Laravel Reverb en un entorno Docker

echo "🚀 Configurando WebSockets para CDATTG Asistence Web"

# Verificar si Docker está ejecutándose
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker no está ejecutándose. Por favor inicia Docker primero."
    exit 1
fi

# Verificar si el contenedor de la aplicación existe
if ! docker ps | grep -q cdattg_app; then
    echo "❌ El contenedor cdattg_app no está ejecutándose."
    echo "   Por favor ejecuta: docker-compose up -d"
    exit 1
fi

echo "✅ Docker y contenedores verificados"

# Configurar variables de entorno para Reverb
echo "📝 Configurando variables de entorno..."

# Verificar si .env existe
if [ ! -f .env ]; then
    echo "❌ Archivo .env no encontrado. Copiando de .env.example..."
    cp .env.example .env
fi

# Agregar configuración de Reverb al .env
if ! grep -q "REVERB_APP_KEY" .env; then
    echo "🔧 Agregando configuración de Reverb al .env..."
    cat >> .env << 'EOF'

# Configuración de Reverb
BROADCAST_DRIVER=reverb
REVERB_APP_KEY=local-app-key
REVERB_APP_SECRET=local-app-secret
REVERB_APP_ID=local-app-id
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
EOF
    echo "✅ Variables de entorno configuradas"
else
    echo "✅ Variables de Reverb ya existen en .env"
fi

# Instalar y configurar Reverb dentro del contenedor
echo "🐳 Configurando Reverb en el contenedor..."

docker exec cdattg_app bash -c "
    echo 'Instalando dependencias...'
    composer install
    
    echo 'Publicando configuración de Reverb...'
    php artisan vendor:publish --provider=\"Laravel\Reverb\ReverbServiceProvider\" --force
    
    echo 'Limpiando cache...'
    php artisan config:clear
    php artisan cache:clear
    
    echo 'Verificando instalación...'
    php artisan reverb:install
"

echo "✅ Reverb configurado en el contenedor"

# Mostrar opciones para iniciar Reverb
echo ""
echo "🎯 Configuración completada!"
echo ""
echo "Para iniciar Reverb, elige una opción:"
echo ""
echo "1. 🐳 Agregar servicio a docker-compose.yml (Recomendado)"
echo "   - Agrega el servicio 'reverb' al docker-compose.yml"
echo "   - Luego ejecuta: docker-compose up -d"
echo ""
echo "2. 🔧 Ejecutar manualmente"
echo "   docker exec -it cdattg_app php artisan reverb:start --host=0.0.0.0 --port=8080"
echo ""
echo "3. 📋 Verificar configuración"
echo "   docker exec cdattg_app php artisan config:show broadcasting"
echo ""
echo "📖 Para más detalles, consulta:"
echo "   - docs/websockets-docker-guide.md"
echo "   - docs/websockets-visitantes.md"
echo ""
echo "🌐 Para probar:"
echo "   http://localhost/websocket-visitantes-example.html"
