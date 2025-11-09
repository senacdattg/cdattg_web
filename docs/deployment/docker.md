# CDATTG Asistence Web - Docker Setup

Esta aplicación Laravel para gestión de programas complementarios del SENA incluye contenerización completa con Docker, incluyendo validación automática de cédulas con Playwright.

## Arquitectura de Contenedores

- **app**: Aplicación Laravel (PHP 8.1 + Node.js)
- **nginx**: Servidor web y proxy reverso
- **db**: Base de datos MySQL 8.0
- **redis**: Cache y sesiones
- **playwright**: Validador de cédulas SenaSofiaPlus

## Requisitos Previos

- Docker >= 20.10
- Docker Compose >= 2.0
- Git

## Instalación y Despliegue

### 1. Clonar el repositorio
```bash
git clone <repository-url>
cd cdattg_asistence_web
```

### 2. Configurar variables de entorno
```bash
cp .env.example .env
# Editar .env con las configuraciones necesarias
```

### 3. Desplegar con Docker
```bash
# Ejecutar el script de construcción
./docker/build.sh

# O manualmente:
docker-compose build
docker-compose run --rm app php artisan migrate --force
docker-compose run --rm app php artisan db:seed --force
docker-compose run --rm app php artisan config:cache
docker-compose run --rm app php artisan route:cache
docker-compose run --rm app php artisan view:cache
docker-compose up -d
```

## Acceder a la Aplicación

- **Aplicación principal**: http://localhost:8000
- **Validador Playwright**: http://localhost:3000
- **Base de datos**: localhost:3306 (usuario: cdattg_user, password: password)

## Comandos Útiles

### Gestión de contenedores
```bash
# Ver logs
docker-compose logs -f

# Detener servicios
docker-compose down

# Reiniciar servicios
docker-compose restart

# Acceder al contenedor de la app
docker-compose exec app bash
```

### Gestión de Laravel
```bash
# Ejecutar comandos de Artisan
docker-compose exec app php artisan <command>

# Limpiar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Gestión de colas
```bash
# Ver estado de las colas
docker-compose exec app php artisan queue:status

# Reiniciar workers de cola
docker-compose exec app php artisan queue:restart
```

## Configuración de Google Drive

Para el almacenamiento de documentos, configurar las siguientes variables en `.env`:

```env
GOOGLE_DRIVE_CLIENT_ID=your_client_id
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret
GOOGLE_DRIVE_REFRESH_TOKEN=your_refresh_token
GOOGLE_DRIVE_FOLDER_ID=your_folder_id
```

## Configuración de Pusher (WebSockets)

```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

## Validación de Cédulas con Playwright

El servicio de Playwright valida automáticamente las cédulas contra SenaSofiaPlus:

- Se ejecuta como un job en segundo plano
- Maneja reintentos automáticos
- Registra el progreso en la base de datos
- Es accesible en el puerto 3000 para debugging

## Estructura de Archivos

```
docker/
├── Dockerfile.app          # Contenedor Laravel
├── Dockerfile.playwright   # Contenedor Playwright
├── docker-compose.yml      # Orquestación
├── nginx.conf             # Configuración Nginx
├── supervisord.conf       # Configuración Supervisor
├── build.sh              # Script de despliegue
└── mysql-init/
    └── init.sql          # Inicialización BD
```

## Solución de Problemas

### Error de conexión a base de datos
```bash
# Verificar que el contenedor de MySQL esté corriendo
docker-compose ps

# Revisar logs de MySQL
docker-compose logs db
```

### Error en validación Playwright
```bash
# Verificar logs del contenedor Playwright
docker-compose logs playwright

# Reiniciar el servicio
docker-compose restart playwright
```

### Problemas de permisos
```bash
# Corregir permisos de storage
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

## Producción

Para despliegue en producción:

1. Configurar SSL/TLS con Let's Encrypt
2. Cambiar `APP_DEBUG=false` en `.env`
3. Configurar variables de entorno seguras
4. Implementar monitoreo y logging centralizado
5. Configurar backups automáticos de base de datos

## Soporte

Para soporte técnico, revisar los logs de la aplicación y contenedores:

```bash
# Logs de Laravel
docker-compose exec app tail -f /var/log/supervisor/*.log

# Logs de Nginx
docker-compose logs nginx