# 📚 Guía de Registro de Asistencias por Jornada con WebSocket

Sistema completo para registrar asistencias de aprendices organizadas por jornada con actualización en tiempo real mediante WebSocket.

## 🎯 Características

- ✅ Registro de entrada y salida de asistencias
- 📊 Organización automática por jornadas (Mañana, Tarde, Noche)
- 🚀 Notificaciones en tiempo real vía WebSocket
- 📡 Actualización automática de información al registrar asistencias
- 🔍 Consulta de asistencias filtradas por jornada y fecha

---

## 🚀 Configuración Inicial

### 1. Configurar el archivo `.env`

Agrega o verifica estas líneas en tu archivo `.env`:

```env
# Para evitar problemas con colas sin MySQL
QUEUE_CONNECTION=sync

# Configuración de Reverb (WebSocket)
BROADCAST_DRIVER=reverb
REVERB_APP_ID=tu_app_id
REVERB_APP_KEY=tu_app_key
REVERB_APP_SECRET=tu_app_secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

### 2. Limpiar caché de configuración

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📡 API Endpoints

### 1. Registrar Entrada

**Endpoint:** `POST /api/asistencia/entrada`

**Headers:**
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Body:**
```json
{
  "instructor_ficha_id": 1,
  "aprendiz_ficha_id": 1,
  "evidencia_id": null
}
```

**Respuesta exitosa (201):**
```json
{
  "status": "success",
  "message": "Entrada registrada exitosamente",
  "asistencia": {
    "id": 123,
    "aprendiz": "Juan Pérez García",
    "hora_ingreso": "08:30:00",
    "jornada": "Mañana",
    "ficha": "2563478",
    "fecha": "2025-09-30 08:30:00"
  }
}
```

**Al mismo tiempo se dispara un evento WebSocket en el canal `asistencias`:**
```javascript
{
  "id": 123,
  "aprendiz": "Juan Pérez García",
  "estado": "entrada",
  "timestamp": "2025-09-30T08:30:00.000000Z",
  "jornada": "Mañana",
  "ficha": "2563478",
  "tipo": "nueva_asistencia"
}
```

---

### 2. Registrar Salida

**Endpoint:** `POST /api/asistencia/salida`

**Body:**
```json
{
  "aprendiz_ficha_id": 1
}
```

**Respuesta exitosa (200):**
```json
{
  "status": "success",
  "message": "Salida registrada exitosamente",
  "asistencia": {
    "id": 123,
    "aprendiz": "Juan Pérez García",
    "hora_ingreso": "08:30:00",
    "hora_salida": "14:30:00",
    "jornada": "Mañana",
    "ficha": "2563478",
    "fecha": "2025-09-30 14:30:00"
  }
}
```

**WebSocket disparado:**
```javascript
{
  "id": 123,
  "aprendiz": "Juan Pérez García",
  "estado": "salida",
  "timestamp": "2025-09-30T14:30:00.000000Z",
  "jornada": "Mañana",
  "ficha": "2563478",
  "tipo": "nueva_asistencia"
}
```

---

### 3. Obtener Asistencias por Jornada

**Endpoint:** `GET /api/asistencia/jornada`

**Query Parameters:**
- `jornada_id` (opcional): ID de la jornada a filtrar
- `fecha` (opcional): Fecha en formato `Y-m-d` (por defecto: hoy)

**Ejemplo:**
```
GET /api/asistencia/jornada?jornada_id=1&fecha=2025-09-30
```

**Respuesta exitosa (200):**
```json
{
  "status": "success",
  "fecha": "2025-09-30",
  "total_asistencias": 25,
  "asistencias": [
    {
      "id": 123,
      "aprendiz": "Juan Pérez García",
      "numero_documento": "1234567890",
      "hora_ingreso": "08:30:00",
      "hora_salida": "14:30:00",
      "ficha": "2563478",
      "jornada": "Mañana",
      "jornada_id": 1,
      "fecha": "2025-09-30",
      "estado": "completa"
    }
  ],
  "por_jornada": {
    "Mañana": [
      { /* asistencias de la mañana */ }
    ],
    "Tarde": [
      { /* asistencias de la tarde */ }
    ],
    "Noche": [
      { /* asistencias de la noche */ }
    ]
  }
}
```

---

### 4. Obtener Fichas con Jornadas

**Endpoint:** `GET /api/asistencia/fichas`

**Respuesta exitosa (200):**
```json
{
  "status": "success",
  "fichas": [
    {
      "id": 1,
      "ficha": "2563478",
      "programa": "Análisis y Desarrollo de Software",
      "jornada": "Mañana",
      "jornada_id": 1
    },
    {
      "id": 2,
      "ficha": "2563479",
      "programa": "Diseño Gráfico",
      "jornada": "Tarde",
      "jornada_id": 2
    }
  ]
}
```

---

## 💻 Comando Artisan para Pruebas

### Registrar Entrada de Prueba

```bash
php artisan asistencia:registrar entrada
```

**Salida:**
```
✅ Asistencia de ENTRADA registrada con éxito!

┌────────────────┬──────────────────────────────┐
│ Campo          │ Valor                        │
├────────────────┼──────────────────────────────┤
│ ID Asistencia  │ 123                          │
│ Aprendiz       │ Juan Pérez García            │
│ Ficha          │ 2563478                      │
│ Jornada        │ Mañana                       │
│ Tipo           │ ENTRADA                      │
│ Hora Ingreso   │ 08:30:00                     │
│ Hora Salida    │ Pendiente                    │
│ Fecha          │ 2025-09-30 08:30:00          │
└────────────────┴──────────────────────────────┘

🚀 Evento de WebSocket disparado correctamente
📡 Los clientes conectados recibirán la notificación en tiempo real
```

### Registrar Salida de Prueba

```bash
php artisan asistencia:registrar salida
```

---

## 🔧 Ejemplos con cURL

### Registrar Entrada

```bash
curl -X POST http://localhost:8000/api/asistencia/entrada \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "instructor_ficha_id": 1,
    "aprendiz_ficha_id": 1,
    "evidencia_id": null
  }'
```

### Registrar Salida

```bash
curl -X POST http://localhost:8000/api/asistencia/salida \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "aprendiz_ficha_id": 1
  }'
```

### Obtener Asistencias por Jornada

```bash
curl -X GET "http://localhost:8000/api/asistencia/jornada?jornada_id=1&fecha=2025-09-30" \
  -H "Accept: application/json"
```

### Obtener Fichas con Jornadas

```bash
curl -X GET http://localhost:8000/api/asistencia/fichas \
  -H "Accept: application/json"
```

---

## 📱 Ejemplo de Cliente JavaScript con WebSocket

```javascript
// Conectar a Laravel Echo / Reverb
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Escuchar eventos de asistencia en tiempo real
window.Echo.channel('asistencias')
    .listen('.NuevaAsistenciaRegistrada', (e) => {
        console.log('Nueva asistencia registrada:', e);
        
        // Actualizar la UI con los nuevos datos
        const mensaje = `${e.aprendiz} - ${e.estado.toUpperCase()}`;
        const jornada = e.jornada;
        const ficha = e.ficha;
        
        // Ejemplo: agregar a una lista
        agregarAsistenciaALista({
            id: e.id,
            aprendiz: e.aprendiz,
            estado: e.estado,
            jornada: jornada,
            ficha: ficha,
            timestamp: e.timestamp
        });
        
        // Ejemplo: mostrar notificación
        mostrarNotificacion(`${mensaje} en ${jornada} - Ficha ${ficha}`);
    });

function agregarAsistenciaALista(asistencia) {
    // Tu lógica para actualizar la interfaz
    const lista = document.getElementById('lista-asistencias');
    const item = document.createElement('div');
    item.className = `asistencia-item ${asistencia.estado}`;
    item.innerHTML = `
        <strong>${asistencia.aprendiz}</strong>
        <span class="badge">${asistencia.estado}</span>
        <small>${asistencia.jornada} - Ficha ${asistencia.ficha}</small>
    `;
    lista.prepend(item);
}

function mostrarNotificacion(mensaje) {
    // Tu lógica para mostrar notificaciones
    alert(mensaje);
}
```

---

## 🎨 Flujo Completo

1. **Cliente envía solicitud** → `POST /api/asistencia/entrada`
2. **Backend valida datos** → Verifica que no exista entrada sin salida
3. **Backend registra asistencia** → Guarda en base de datos
4. **Backend dispara evento** → `NuevaAsistenciaRegistrada`
5. **WebSocket transmite** → Evento al canal `asistencias`
6. **Todos los clientes conectados** → Reciben actualización en tiempo real
7. **UI se actualiza automáticamente** → Sin necesidad de recargar página

---

## ⚠️ Errores Comunes

### Error: "No se puede establecer una conexión"

**Problema:** No tienes configurado `QUEUE_CONNECTION=sync`

**Solución:**
```bash
# Agregar a .env
QUEUE_CONNECTION=sync

# Limpiar configuración
php artisan config:clear
```

### Error: "No se encontró ningún aprendiz"

**Problema:** No hay datos de prueba en la base de datos

**Solución:** Ejecuta los seeders o crea datos manualmente en la base de datos.

### WebSocket no funciona

**Problema:** Reverb no está corriendo

**Solución:**
```bash
# Iniciar servidor Reverb
php artisan reverb:start
```

---

## 📊 Casos de Uso

### Caso 1: Sistema de Asistencias para Instructores

Un instructor puede:
1. Ver todas las asistencias del día en su jornada
2. Recibir notificaciones en tiempo real cuando un aprendiz registra entrada/salida
3. Filtrar asistencias por fecha y jornada

### Caso 2: Panel de Control en Tiempo Real

Un administrador puede:
1. Ver todas las asistencias de todas las jornadas en tiempo real
2. Recibir actualizaciones instantáneas sin recargar la página
3. Exportar reportes por jornada y fecha

### Caso 3: Aplicación Móvil para Aprendices

Un aprendiz puede:
1. Registrar su entrada usando la app móvil
2. Registrar su salida al finalizar la jornada
3. Ver su historial de asistencias

---

## 🛠️ Arquitectura

```
Cliente (Frontend/App)
    ↓
    ↓ POST /api/asistencia/entrada
    ↓
RegistroAsistenciaController
    ↓
    ├─→ Valida datos
    ├─→ Guarda en DB (AsistenciaAprendiz)
    ├─→ Dispara evento (NuevaAsistenciaRegistrada)
    │       ↓
    │       ↓ Broadcasting
    │       ↓
    └─→ WebSocket (Reverb)
            ↓
            ↓ Canal: asistencias
            ↓
        Todos los clientes conectados
            ↓
        Actualización en tiempo real
```

---

## 📝 Notas Importantes

- ✅ Los eventos se disparan **automáticamente** al registrar asistencias
- ✅ **No necesitas** ejecutar queue workers con `QUEUE_CONNECTION=sync`
- ✅ Las asistencias se **agrupan automáticamente** por jornada
- ✅ El WebSocket funciona en **tiempo real** sin polling
- ✅ Compatible con **múltiples clientes** conectados simultáneamente

---

## 🎓 Principios Aplicados

- **SRP (Single Responsibility Principle)**: Cada controlador/comando tiene una responsabilidad única
- **KISS (Keep It Simple, Stupid)**: Código claro y directo
- **DRY (Don't Repeat Yourself)**: Reutilización de código mediante relaciones Eloquent
- **Arquitectura Modular**: Separación clara de responsabilidades

---

¿Necesitas más ayuda? Revisa los logs en `storage/logs/laravel.log` 📋
