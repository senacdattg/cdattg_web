# Configuración de Laravel Reverb

## Variables de entorno necesarias

Agrega estas variables a tu archivo `.env`:

```env
# Driver de broadcasting por defecto
BROADCAST_DRIVER=reverb

# Configuración del servidor Reverb
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_SERVER_PATH=

# Configuración de la aplicación Reverb
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local

# Configuración del host público (para producción)
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Configuración de escalado (opcional, para producción)
REVERB_SCALING_ENABLED=false
REVERB_SCALING_CHANNEL=reverb

# Configuración de Redis para escalado (si está habilitado)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=
REDIS_PORT=6379
REDIS_DB=0

# Configuración de conexiones máximas
REVERB_APP_MAX_CONNECTIONS=100
REVERB_APP_MAX_MESSAGE_SIZE=10000

# Configuración de ping y timeout
REVERB_APP_PING_INTERVAL=60
REVERB_APP_ACTIVITY_TIMEOUT=30

# Configuración de Pulse y Telescope (opcional)
REVERB_PULSE_INGEST_INTERVAL=15
REVERB_TELESCOPE_INGEST_INTERVAL=15
```

## Comandos para ejecutar Reverb

### Desarrollo local (accesible desde toda la red)
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

### Con debug habilitado
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug
```

### Solo localhost (no accesible desde red)
```bash
php artisan reverb:start --host=127.0.0.1 --port=8080
```

### Con escalado habilitado
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080 --scaling
```

## Configuración del cliente JavaScript

El archivo `resources/js/echo-config.js` ya está configurado para usar Reverb. Asegúrate de importarlo en tu aplicación:

```javascript
import './echo-config.js';
```

## Migración desde beyondcode/laravel-websockets

1. ✅ Instalado Laravel Reverb
2. ✅ Configurado broadcasting.php
3. ✅ Creado configuración de Echo para Reverb
4. ✅ Los eventos existentes son compatibles
5. ⏳ Configurar variables de entorno
6. ⏳ Probar la conexión

## Notas importantes

- Reverb es compatible con PHP 8.4 y Laravel 12
- Los eventos existentes (`NuevaAsistenciaRegistrada`, `QrScanned`) funcionan sin cambios
- El cliente JavaScript usa la misma API de Echo
- Para producción, considera usar HTTPS y configurar el escalado con Redis
