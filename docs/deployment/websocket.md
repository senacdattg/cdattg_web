# 🔌 WebSocket para Sistema de Asistencias con Laravel Reverb

Este documento explica cómo configurar y utilizar el WebSocket para notificaciones en tiempo real del sistema de asistencias usando **Laravel Reverb**, la solución oficial moderna de Laravel para WebSockets.

## 📋 Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Configuración del Backend](#configuración-del-backend)
- [Instalación y Configuración](#instalación-y-configuración)
- [Arrancar los Servicios](#arrancar-los-servicios)
- [Probar el WebSocket](#probar-el-websocket)
- [Integración con Flutter](#integración-con-flutter)
- [API de Eventos](#api-de-eventos)
- [Configuración para Producción](#configuración-para-producción)
- [Troubleshooting](#troubleshooting)

## 🔧 Requisitos Previos

- **PHP 8.2+** (compatible con PHP 8.4)
- **Laravel 12+**
- **Composer**
- **Node.js 16+**
- **npm o yarn**

## ⚙️ Configuración del Backend

### 1. Variables de Entorno

Agrega estas variables a tu archivo `.env`:

```env
# WebSocket Configuration - Laravel Reverb
BROADCAST_DRIVER=reverb

# Configuración del servidor Reverb
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_SERVER_PATH=

# Configuración de la aplicación Reverb
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local

# Configuración del host público (usa tu IP local o dominio)
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Configuración de escalado (opcional)
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

# Queue Configuration (recomendado)
QUEUE_CONNECTION=database
```

### 2. Instalar Dependencias

```bash
# Dependencias de PHP (Laravel Reverb ya está instalado)
composer install

# Dependencias de Node.js
npm install

# Compilar assets
npm run build
```

### 3. Configuración de Broadcasting

El archivo `config/broadcasting.php` ya está configurado para usar Reverb como driver por defecto.

## 🚀 Arrancar los Servicios

**💡 Nota**: Para acceder desde dispositivos móviles en tu red local, necesitarás tu IP local. Ejecuta:
```bash
# En Windows
ipconfig
# Busca "Dirección IPv4" en tu adaptador de red activo

# En Linux/Mac
hostname -I
# o
ifconfig
```

### Terminal 1: Servidor Laravel
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
*El servidor estará disponible en: http://localhost:8000 o http://tu-ip:8000*

### Terminal 2: Servidor Reverb
```bash
# Desarrollo local (accesible desde toda la red)
php artisan reverb:start --host=0.0.0.0 --port=8080

# Con debug habilitado
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug

# Solo localhost (no accesible desde red)
php artisan reverb:start --host=127.0.0.1 --port=8080
```
*El WebSocket estará disponible en: ws://localhost:8080 o ws://tu-ip:8080*

### Terminal 3: Worker de Colas (Recomendado)
```bash
php artisan queue:work
```

### Terminal 4: Compilar Assets en Modo Desarrollo (Opcional)
```bash
npm run dev
```

## 🧪 Probar el WebSocket

### Opción 1: Comando de Prueba
```bash
# Probar evento de QR
php artisan websocket:test qr

# Probar evento de asistencia
php artisan websocket:test asistencia
```

### Opción 2: Prueba Manual
1. Abre http://localhost:8000/asistence/web en tu navegador
2. Abre las herramientas de desarrollador (F12)
3. Ve a la consola
4. Deberías ver: `"WebSocket channels configurados correctamente"`
5. Escanea un QR o ejecuta el comando de prueba
6. Verás las notificaciones en tiempo real

### 📍 Rutas Disponibles para WebSocket

#### Rutas de Asistencia con QR:
- **Página principal de QR**: http://localhost:8000/asistence/web
- **Lista de asistencias**: http://localhost:8000/asistencia/index
- **Selección de caracterización**: `/asistence/caracterSelected/{caracterizacion}/{evidencia}`

#### Rutas API para WebSocket:
- **Verificar documento**: `POST /verify-document`
- **Registrar asistencia**: `POST /asistence/store`
- **Finalizar asistencia**: `POST /asistence/finalizar-asistencia`

### 🔐 Requisitos de Autenticación

**IMPORTANTE**: Todas las rutas de WebSocket requieren:
1. **Autenticación**: Debes estar logueado en el sistema
2. **Permisos**: Necesitas el permiso `TOMAR ASISTENCIA` o `VER PROGRAMA DE CARACTERIZACION`

#### Para probar sin autenticación:
1. Ve a http://localhost:8000/login
2. Inicia sesión con un usuario válido
3. Luego accede a http://localhost:8000/asistence/web

## 📱 Integración con Flutter

### 1. Dependencias en Flutter

Agrega estas dependencias a tu `pubspec.yaml`:

```yaml
dependencies:
  pusher_channels_flutter: ^2.2.2
  # o alternativamente:
  # web_socket_channel: ^2.4.0
```

### 2. Configuración del Cliente Flutter para Reverb

**IMPORTANTE**: Si estás accediendo desde un dispositivo móvil en tu red local, reemplaza `127.0.0.1` con la IP de tu máquina (ej: `192.168.1.100`).

```dart
import 'package:pusher_channels_flutter/pusher_channels_flutter.dart';

class WebSocketService {
  static PusherChannelsFlutter? pusher;
  
  static Future<void> initialize() async {
    try {
      await PusherChannelsFlutter.init(
        apiKey: "local", // REVERB_APP_KEY del .env
        cluster: "mt1",   // Cluster por defecto
        hostEndPoint: "192.168.1.100", // IP de tu máquina (no usar 127.0.0.1 desde móvil)
        port: 8080,                // REVERB_PORT del .env
        encrypted: false,          // true si usas HTTPS
      );
      
      pusher = PusherChannelsFlutter.getInstance();
      await pusher!.connect();
      
      print("WebSocket Reverb conectado exitosamente");
    } catch (e) {
      print("Error conectando WebSocket: $e");
    }
  }
  
  // Suscribirse al canal de asistencias
  static void subscribeToAsistencias() {
    pusher?.subscribe(
      channelName: "asistencias",
      onEvent: (event) {
        print("Evento recibido: ${event.eventName}");
        print("Datos: ${event.data}");
        
        if (event.eventName == "NuevaAsistenciaRegistrada") {
          _handleNuevaAsistencia(event.data);
        }
      },
    );
  }
  
  // Suscribirse al canal de QR
  static void subscribeToQR() {
    pusher?.subscribe(
      channelName: "qr-scans",
      onEvent: (event) {
        print("QR escaneado: ${event.data}");
        
        if (event.eventName == "QrScanned") {
          _handleQRScanned(event.data);
        }
      },
    );
  }
  
  static void _handleNuevaAsistencia(String data) {
    // Parsear JSON y manejar la nueva asistencia
    final Map<String, dynamic> asistenciaData = jsonDecode(data);
    
    print("Nueva asistencia: ${asistenciaData['aprendiz']}");
    print("Estado: ${asistenciaData['estado']}");
    print("Timestamp: ${asistenciaData['timestamp']}");
    
    // Aquí puedes actualizar tu UI, mostrar notificaciones, etc.
    _showNotification(
      title: "Nueva Asistencia",
      body: "${asistenciaData['aprendiz']} registró asistencia",
    );
  }
  
  static void _handleQRScanned(String data) {
    // Manejar escaneo de QR
    final Map<String, dynamic> qrData = jsonDecode(data);
    print("QR escaneado: ${qrData['aprendiz_nombre']}");
  }
  
  static void _showNotification(String title, String body) {
    // Implementar notificación local
    // Puedes usar flutter_local_notifications o similar
  }
  
  static void disconnect() {
    pusher?.disconnect();
  }
}
```

### 3. Uso en tu App Flutter

```dart
import 'package:flutter/material.dart';

class AsistenciasScreen extends StatefulWidget {
  @override
  _AsistenciasScreenState createState() => _AsistenciasScreenState();
}

class _AsistenciasScreenState extends State<AsistenciasScreen> {
  @override
  void initState() {
    super.initState();
    _initializeWebSocket();
  }
  
  Future<void> _initializeWebSocket() async {
    await WebSocketService.initialize();
    WebSocketService.subscribeToAsistencias();
    WebSocketService.subscribeToQR();
  }
  
  @override
  void dispose() {
    WebSocketService.disconnect();
    super.dispose();
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Asistencias en Tiempo Real")),
      body: StreamBuilder(
        // Tu UI aquí
        builder: (context, snapshot) {
          return ListView.builder(
            itemCount: asistencias.length,
            itemBuilder: (context, index) {
              return ListTile(
                title: Text(asistencias[index].aprendiz),
                subtitle: Text("Estado: ${asistencias[index].estado}"),
                trailing: Text(asistencias[index].timestamp),
              );
            },
          );
        },
      ),
    );
  }
}
```

## 📡 API de Eventos

### Canal: `asistencias`

#### Evento: `NuevaAsistenciaRegistrada`
```json
{
  "id": 123,
  "aprendiz": "Juan Pérez",
  "estado": "entrada",
  "timestamp": "2024-01-15T10:30:00.000Z",
  "tipo": "nueva_asistencia"
}
```

### Canal: `qr-scans`

#### Evento: `QrScanned`
```json
{
  "numero_documento": "12345678",
  "aprendiz_nombre": "Juan Pérez",
  "ficha_id": 1,
  "hora_ingreso": "10:30:00",
  "tipo": "entrada",
  "instructor_id": 1
}
```

## 🔧 Configuración para Producción

### Variables de Entorno para Producción
```env
BROADCAST_DRIVER=reverb

# Configuración del servidor Reverb para producción
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Configuración de la aplicación Reverb
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret

# Configuración del host público
REVERB_HOST=your_domain.com
REVERB_PORT=443
REVERB_SCHEME=https

# Habilitar escalado para producción
REVERB_SCALING_ENABLED=true
REVERB_SCALING_CHANNEL=reverb

# Configuración de Redis para escalado
REDIS_HOST=your_redis_host
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379
REDIS_DB=0

# Configuración de conexiones máximas
REVERB_APP_MAX_CONNECTIONS=1000
REVERB_APP_MAX_MESSAGE_SIZE=10000
```

### Configuración de Flutter para Producción
```dart
await PusherChannelsFlutter.init(
  apiKey: "your_app_key",
  cluster: "mt1",
  hostEndPoint: "your_domain.com",
  port: 443,
  encrypted: true,
);
```

### Comando para Producción con Escalado
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080 --scaling
```

## 🐛 Troubleshooting

### Problema: Error 404 - "Not Found" en rutas de WebSocket
**Solución:**
1. **Verifica la URL correcta**: Usa `/asistence/web` en lugar de `/qr-asistence`
2. **Confirma autenticación**: Debes estar logueado en el sistema
3. **Verifica permisos**: Tu usuario debe tener el permiso `TOMAR ASISTENCIA`
4. **Revisa las rutas**: Ejecuta `php artisan route:list --name=asistence` para ver todas las rutas disponibles

### Problema: "WebSocket channels configurados correctamente" no aparece
**Solución:**
1. Verifica que el archivo `websocket-handler.js` esté incluido en tu vista
2. Asegúrate de que Laravel Echo esté configurado correctamente
3. Revisa la consola del navegador para errores
4. Confirma que el servidor Reverb esté corriendo en el puerto 8080

### Problema: Eventos no llegan a Flutter
**Solución:**
1. Verifica que el puerto 8080 esté abierto
2. Confirma que las credenciales de Reverb coincidan
3. Revisa los logs del servidor Reverb
4. Asegúrate de que el worker de colas esté corriendo

### Problema: "Connection refused" en Flutter
**Solución:**
1. Asegúrate de que el servidor Reverb esté corriendo: `php artisan reverb:start`
2. Verifica la configuración de red y firewall
3. Para desarrollo local, usa la IP de tu máquina en lugar de localhost
4. Confirma que el puerto 8080 esté disponible

### Problema: Eventos no se disparan
**Solución:**
1. Verifica que el worker de colas esté corriendo: `php artisan queue:work`
2. Revisa que los eventos implementen `ShouldBroadcast`
3. Confirma que los canales estén registrados correctamente
4. Prueba con el comando: `php artisan websocket:test asistencia`

### Problema: Error de compatibilidad con PHP 8.4
**Solución:**
- ✅ **Resuelto**: Laravel Reverb es completamente compatible con PHP 8.4
- ✅ **Migrado desde**: beyondcode/laravel-websockets (incompatible)
- ✅ **Configuración actualizada**: Broadcasting configurado para Reverb

## 📞 Soporte

Si tienes problemas:

1. **Revisa los logs de Laravel**: `storage/logs/laravel.log`
2. **Verifica la consola del navegador** para errores de JavaScript
3. **Revisa los logs del Reverb** en la terminal donde corre `reverb:start`
4. **Prueba la conectividad** con el comando: `php artisan websocket:test asistencia`
5. **Verifica el estado del servidor**: `netstat -an | findstr :8080`

## 🔄 Comandos Útiles

```bash
# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Reiniciar workers de cola
php artisan queue:restart

# Ver colas pendientes
php artisan queue:failed

# Probar conectividad WebSocket
php artisan websocket:test asistencia

# Verificar estado del servidor Reverb
netstat -an | findstr :8080

# Iniciar Reverb con debug
php artisan reverb:start --host=127.0.0.1 --port=8080 --debug

# Iniciar Reverb para producción
php artisan reverb:start --host=0.0.0.0 --port=8080
```

## 🆕 Ventajas de Laravel Reverb

- ✅ **Compatible con PHP 8.4** y Laravel 12
- ✅ **Solución oficial** de Laravel (no terceros)
- ✅ **Mejor rendimiento** y estabilidad
- ✅ **API idéntica** a beyondcode/laravel-websockets
- ✅ **Escalado horizontal** con Redis
- ✅ **Integración nativa** con Laravel Pulse y Telescope
- ✅ **Configuración simplificada**
- ✅ **Soporte oficial** y mantenimiento activo

---

**¡Listo!** Ahora tienes un sistema completo de WebSocket funcionando con **Laravel Reverb** y soporte para aplicaciones Flutter. 🚀

**Migración completada exitosamente desde beyondcode/laravel-websockets a Laravel Reverb.**