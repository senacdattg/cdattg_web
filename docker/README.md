# 🐳 Guía de Docker - CDATTG Asistencia Web

## 📋 Descripción

Esta es una implementación simplificada de Docker para desarrollo local. Las dependencias se instalan dentro del contenedor cuando se inicia, no durante el build, lo que hace que el proceso sea mucho más rápido y confiable.

## 🚀 Inicio Rápido

### Opción 1: Script Automático (Recomendado)

```bash
./docker/init.sh
```

Este script:
- Construye las imágenes Docker
- Inicia los servicios (DB, Redis)
- Instala dependencias dentro del contenedor
- Ejecuta migraciones y seeders
- Configura la aplicación

### Opción 2: Manual

```bash
# 1. Construir imágenes
docker-compose build app

# 2. Iniciar servicios
docker-compose up -d

# 3. Instalar dependencias (dentro del contenedor)
docker-compose exec app composer install --no-dev --optimize-autoloader --prefer-dist
docker-compose exec app npm ci

# 4. Compilar assets
docker-compose exec app npm run build

# 5. Ejecutar migraciones
docker-compose exec app php artisan migrate:module --all --fresh

# 6. Ejecutar seeders
docker-compose exec app php artisan db:seed --force
```

## 📝 Comandos Útiles

### Gestión de Contenedores

```bash
# Ver logs
docker-compose logs -f app

# Acceder al contenedor
docker-compose exec app sh

# Reiniciar servicios
docker-compose restart

# Detener servicios
docker-compose down

# Detener y eliminar volúmenes
docker-compose down -v
```

### Comandos Laravel

```bash
# Ejecutar migraciones
docker-compose exec app php artisan migrate:module --all --fresh

# Ejecutar seeders
docker-compose exec app php artisan db:seed --force

# Limpiar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Optimizar
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Ver rutas
docker-compose exec app php artisan route:list
```

### Instalar/Actualizar Dependencias

```bash
# Composer
docker-compose exec app composer install
docker-compose exec app composer update

# NPM
docker-compose exec app npm install
docker-compose exec app npm run build
docker-compose exec app npm run dev
```

## 🌐 URLs

- **Aplicación Web**: http://localhost:8000
- **WebSocket (Reverb)**: ws://localhost:8080
- **Playwright Validator**: http://localhost:3000

## 🔧 Configuración

### Variables de Entorno

Crea un archivo `.env` en la raíz del proyecto con:

```env
APP_ENV=local
APP_DEBUG=true
APP_KEY=

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cdattg
DB_USERNAME=cdattg_user
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PORT=6379
QUEUE_CONNECTION=redis

BROADCAST_DRIVER=reverb
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Cambiar Credenciales de Base de Datos

Puedes sobrescribir las credenciales usando variables de entorno:

```bash
DB_ROOT_PASSWORD=mipassword DB_USERNAME=mi_usuario DB_PASSWORD=mi_password docker-compose up -d
```

O crear un archivo `.env` con estas variables.

## 🐛 Solución de Problemas

### El contenedor no inicia

```bash
# Ver logs
docker-compose logs app

# Verificar estado
docker-compose ps
```

### Error de permisos

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Base de datos no conecta

```bash
# Verificar que MySQL está corriendo
docker-compose ps db

# Ver logs de MySQL
docker-compose logs db

# Reiniciar base de datos
docker-compose restart db
```

### Reconstruir todo desde cero

```bash
docker-compose down -v
docker-compose build --no-cache app
./docker/init.sh
```

## 📦 Estructura

```
docker/
├── Dockerfile.app          # Dockerfile de la aplicación PHP
├── Dockerfile.playwright   # Dockerfile de Playwright
├── nginx.conf              # Configuración de Nginx
├── supervisord.conf        # Configuración de Supervisor
├── entrypoint.sh           # Script de inicialización del contenedor
├── init.sh                 # Script de inicialización del proyecto
└── mysql-init/             # Scripts SQL de inicialización
```

## 🔄 Flujo de Trabajo

1. **Desarrollo**: Modifica el código en tu máquina local, los cambios se reflejan inmediatamente en el contenedor gracias a los volúmenes.

2. **Dependencias**: Si necesitas instalar nuevas dependencias:
   - PHP: `docker-compose exec app composer require paquete/nombre`
   - Node: `docker-compose exec app npm install paquete`

3. **Migraciones**: Usa el comando personalizado:
   ```bash
   docker-compose exec app php artisan migrate:module --all --fresh
   ```

4. **Assets**: Compila assets cuando hagas cambios:
   ```bash
   docker-compose exec app npm run build
   ```

## ⚡ Ventajas de esta Implementación

- ✅ **Build rápido**: Solo instala PHP y extensiones durante el build
- ✅ **Dependencias flexibles**: Se instalan dentro del contenedor al iniciar
- ✅ **Desarrollo ágil**: Cambios en código se reflejan inmediatamente
- ✅ **Sin problemas de red**: Las dependencias se instalan cuando el contenedor está corriendo
- ✅ **Fácil de mantener**: Estructura simple y clara

