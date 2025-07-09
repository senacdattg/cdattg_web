# Configuración de WebSockets para Notificaciones en Tiempo Real

Este documento explica cómo configurar y usar el sistema de WebSockets para notificaciones en tiempo real cuando se escanean códigos QR o se registran asistencias.

## Características

- ✅ Notificaciones en tiempo real cuando se escanea un código QR
- ✅ Notificaciones cuando se registra una nueva asistencia
- ✅ Actualización automática de la interfaz de usuario
- ✅ Notificaciones toast usando AdminLTE
- ✅ Efectos visuales para mejor experiencia de usuario

## Requisitos Previos

1. **Laravel Echo Server** o **Laravel WebSockets**
2. **Pusher** (para producción) o configuración local
3. **Node.js** y **npm**

## Instalación

### 1. Instalar dependencias de Node.js

```bash
npm install laravel-echo pusher-js
```

### 2. Configurar variables de entorno

Agrega estas variables a tu archivo `.env`:

```env
# WebSocket Configuration
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
```

### 3. Para desarrollo local (Laravel WebSockets)

Si quieres usar Laravel WebSockets para desarrollo local:

```bash
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
```

Y configura tu `.env` para desarrollo local:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=12345
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

### 4. Compilar assets

```bash
npm run build
```

## Uso

### Eventos Automáticos

Los eventos se disparan automáticamente:

1. **QrScanned**: Cuando se escanea un código QR en `AsistenceQrController::verifyDocument()`
2. **AsistenciaCreated**: Cuando se crea una nueva asistencia (mediante el observer)

### Incluir en las vistas

Para incluir las notificaciones en tiempo real en cualquier vista, agrega:

```html
@section('js')
    <script src="{{ asset('js/websocket-handler.js') }}"></script>
@endsection
```

### Probar los WebSockets

Usa el comando Artisan para probar:

```bash
# Probar evento de QR
php artisan websocket:test qr

# Probar evento de asistencia
php artisan websocket:test asistencia
```

## Canales de WebSocket

### `qr-scans`
- **Evento**: `QrScanned`
- **Datos**: Información del QR escaneado
- **Uso**: Notificaciones de escaneo de códigos QR

### `asistencias`
- **Evento**: `AsistenciaCreated`
- **Datos**: Información de la asistencia creada
- **Uso**: Notificaciones de nuevas asistencias

## Estructura de Datos

### QrScanned Event
```json
{
    "type": "qr_scanned",
    "data": {
        "numero_documento": "12345678",
        "aprendiz_nombre": "Juan Pérez",
        "ficha_id": 1,
        "hora_ingreso": "14:30:00",
        "tipo": "entrada",
        "instructor_id": 1
    },
    "timestamp": "2024-01-01T14:30:00.000000Z"
}
```

### AsistenciaCreated Event
```json
{
    "type": "asistencia_created",
    "data": {
        "id": 1,
        "instructor_ficha_id": 1,
        "aprendiz_ficha_id": 1,
        "hora_ingreso": "14:30:00",
        "hora_salida": null,
        "aprendiz": {
            "id": 1,
            "persona": {
                "nombre_completo": "Juan Pérez",
                "numero_documento": "12345678"
            }
        },
        "ficha": {
            "id": 1,
            "numero_ficha": "123456"
        }
    },
    "timestamp": "2024-01-01T14:30:00.000000Z"
}
```

## Personalización

### Modificar notificaciones

Edita el archivo `public/js/websocket-handler.js` para personalizar:

- Tipos de notificaciones
- Efectos visuales
- Actualización de interfaz
- Formato de mensajes

### Agregar nuevos eventos

1. Crea un nuevo evento en `app/Events/`
2. Implementa `ShouldBroadcast`
3. Define el canal en `routes/channels.php`
4. Agrega el listener en `websocket-handler.js`

## Solución de Problemas

### WebSockets no funcionan
1. Verifica que `BROADCAST_DRIVER=pusher` en `.env`
2. Asegúrate de que Laravel Echo Server esté ejecutándose
3. Revisa la consola del navegador para errores

### Eventos no se disparan
1. Verifica que los observers estén registrados en `AppServiceProvider`
2. Revisa los logs de Laravel
3. Usa el comando de prueba: `php artisan websocket:test`

### Notificaciones no aparecen
1. Verifica que AdminLTE esté cargado
2. Revisa que el archivo `websocket-handler.js` esté incluido
3. Verifica la consola del navegador para errores de JavaScript

## Producción

Para producción, se recomienda:

1. Usar **Pusher** como servicio de WebSockets
2. Configurar SSL/TLS
3. Optimizar la configuración de Redis si se usa
4. Monitorear el rendimiento de los WebSockets

## Comandos Útiles

```bash
# Probar WebSockets
php artisan websocket:test qr
php artisan websocket:test asistencia

# Limpiar cache
php artisan config:clear
php artisan cache:clear

# Reiniciar servicios
php artisan queue:restart
``` 
