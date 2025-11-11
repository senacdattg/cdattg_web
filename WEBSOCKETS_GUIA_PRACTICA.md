# Guía Práctica: WebSockets y Endpoints para Visitantes

## Descripción General

Esta guía proporciona instrucciones prácticas para consumir los endpoints HTTP y WebSockets disponibles para gestionar visitantes y estadísticas en tiempo real.

## Endpoints HTTP Disponibles

### 1. Obtener Estadísticas
**GET** `/api/websocket/estadisticas`

Obtiene las estadísticas actuales del sistema.

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "roles": {
      "super_administradores": {
        "total": 2,
        "activos": 2,
        "inactivos": 0
      },
      "administradores": {
        "total": 5,
        "activos": 4,
        "inactivos": 1
      },
      "instructores": {
        "total": 25,
        "activos": 22,
        "inactivos": 3
      },
      "visitantes": {
        "total": 15,
        "activos": 12,
        "inactivos": 3
      },
      "aprendices": {
        "total": 150,
        "activos": 145,
        "inactivos": 5
      },
      "aspirantes": {
        "total": 30,
        "activos": 28,
        "inactivos": 2
      }
    },
    "asistencias_hoy": 89
  }
}
```

**Ejemplo de uso en JavaScript:**
```javascript
async function obtenerEstadisticas() {
  try {
    const response = await fetch('/api/websocket/estadisticas');
    const data = await response.json();
    
    if (data.success) {
      console.log('Estadísticas:', data.data);
      return data.data;
    }
  } catch (error) {
    console.error('Error obteniendo estadísticas:', error);
  }
}
```

### 2. Registrar Entrada de Visitante
**POST** `/api/websocket/entrada`

Registra la entrada de un visitante y emite evento por WebSocket.

**Parámetros:**
```json
{
  "persona_id": 123,
  "nombre": "Juan Pérez",
  "documento": "12345678",
  "rol": "Aprendiz",
  "ficha": "123456",
  "ambiente": "Aula 101"
}
```

**Ejemplo de uso:**
```javascript
async function registrarEntrada(visitante) {
  try {
    const response = await fetch('/api/websocket/entrada', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(visitante)
    });
    
    const data = await response.json();
    
    if (data.success) {
      console.log('Entrada registrada:', data.visitante);
      return data;
    }
  } catch (error) {
    console.error('Error registrando entrada:', error);
  }
}

// Uso:
registrarEntrada({
  persona_id: 123,
  nombre: "Juan Pérez",
  documento: "12345678",
  rol: "Aprendiz",
  ficha: "123456",
  ambiente: "Aula 101"
});
```

### 3. Registrar Salida de Visitante
**POST** `/api/websocket/salida`

Registra la salida de un visitante y emite evento por WebSocket.

**Parámetros:**
```json
{
  "persona_id": 123,
  "nombre": "Juan Pérez",
  "documento": "12345678",
  "rol": "Aprendiz"
}
```

**Ejemplo de uso:**
```javascript
async function registrarSalida(visitante) {
  try {
    const response = await fetch('/api/websocket/salida', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(visitante)
    });
    
    const data = await response.json();
    
    if (data.success) {
      console.log('Salida registrada:', data.visitante);
      return data;
    }
  } catch (error) {
    console.error('Error registrando salida:', error);
  }
}
```

### 4. Obtener Visitantes Actuales
**GET** `/api/websocket/visitantes-actuales`

Obtiene la lista de visitantes actualmente en el sistema.

## WebSockets en Tiempo Real

### Configuración del Cliente

```javascript
// Configuración para desarrollo local
const REVERB_APP_KEY = 'local-app-key';
const WS_HOST = 'localhost:8081';

// Conectar WebSocket
const websocket = new WebSocket(`ws://${WS_HOST}/app/${REVERB_APP_KEY}`);

websocket.onopen = function() {
  console.log('WebSocket conectado');
  
  // Suscribirse a canales
  const subscribeVisitantes = {
    event: 'pusher:subscribe',
    data: { channel: 'visitantes' }
  };
  websocket.send(JSON.stringify(subscribeVisitantes));
  
  const subscribeEstadisticas = {
    event: 'pusher:subscribe',
    data: { channel: 'estadisticas-visitantes' }
  };
  websocket.send(JSON.stringify(subscribeEstadisticas));
};

websocket.onmessage = function(event) {
  const data = JSON.parse(event.data);
  
  if (data.event === 'visitante.actualizado') {
    console.log('Visitante actualizado:', data.data);
    // Actualizar interfaz con datos del visitante
  } else if (data.event === 'estadisticas.actualizadas') {
    console.log('Estadísticas actualizadas:', data.data);
    // Actualizar estadísticas en tiempo real
  }
};

websocket.onerror = function(error) {
  console.error('Error en WebSocket:', error);
};

websocket.onclose = function() {
  console.log('WebSocket desconectado');
};
```

### Eventos Disponibles

#### 1. Evento: `visitante.actualizado`
Se emite cuando un visitante entra o sale del sistema.

**Estructura del evento:**
```json
{
  "event": "visitante.actualizado",
  "data": {
    "visitante": {
      "id": 123,
      "nombre": "Juan Pérez",
      "documento": "12345678",
      "rol": "Aprendiz",
      "ficha": "123456",
      "ambiente": "Aula 101",
      "hora_entrada": "2024-01-15T10:30:00Z"
    },
    "tipo": "entrada",
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

#### 2. Evento: `estadisticas.actualizadas`
Se emite cuando se actualizan las estadísticas del sistema.

**Estructura del evento:**
```json
{
  "event": "estadisticas.actualizadas",
  "data": {
    "estadisticas": {
      "roles": {
        "super_administradores": { "total": 2, "activos": 2, "inactivos": 0 },
        "administradores": { "total": 5, "activos": 4, "inactivos": 1 },
        "instructores": { "total": 25, "activos": 22, "inactivos": 3 },
        "visitantes": { "total": 15, "activos": 12, "inactivos": 3 },
        "aprendices": { "total": 150, "activos": 145, "inactivos": 5 },
        "aspirantes": { "total": 30, "activos": 28, "inactivos": 2 }
      },
      "asistencias_hoy": 89
    },
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

## Ejemplo Completo de Integración

```javascript
class VisitantesManager {
  constructor() {
    this.estadisticas = null;
    this.visitantesActuales = [];
    this.init();
  }

  async init() {
    // Cargar estadísticas iniciales
    await this.cargarEstadisticas();
    
    // Conectar WebSocket
    this.conectarWebSocket();
  }

  async cargarEstadisticas() {
    try {
      const response = await fetch('/api/websocket/estadisticas');
      const data = await response.json();
      
      if (data.success) {
        this.estadisticas = data.data;
        this.actualizarUI();
      }
    } catch (error) {
      console.error('Error cargando estadísticas:', error);
    }
  }

  conectarWebSocket() {
    const websocket = new WebSocket('ws://localhost:8081/app/local-app-key');
    
    websocket.onopen = () => {
      console.log('Conectado a WebSocket');
      
      // Suscribirse a canales
      websocket.send(JSON.stringify({
        event: 'pusher:subscribe',
        data: { channel: 'visitantes' }
      }));
      
      websocket.send(JSON.stringify({
        event: 'pusher:subscribe',
        data: { channel: 'estadisticas-visitantes' }
      }));
    };

    websocket.onmessage = (event) => {
      const data = JSON.parse(event.data);
      
      switch (data.event) {
        case 'visitante.actualizado':
          this.manejarVisitanteActualizado(data.data);
          break;
        case 'estadisticas.actualizadas':
          this.manejarEstadisticasActualizadas(data.data);
          break;
      }
    };
  }

  manejarVisitanteActualizado(data) {
    if (data.tipo === 'entrada') {
      this.visitantesActuales.push(data.visitante);
    } else if (data.tipo === 'salida') {
      this.visitantesActuales = this.visitantesActuales.filter(
        v => v.id !== data.visitante.id
      );
    }
    
    this.actualizarUI();
  }

  manejarEstadisticasActualizadas(data) {
    this.estadisticas = data.estadisticas;
    this.actualizarUI();
  }

  actualizarUI() {
    // Actualizar la interfaz de usuario con los datos actualizados
    console.log('Estadísticas:', this.estadisticas);
    console.log('Visitantes actuales:', this.visitantesActuales);
  }

  async registrarEntrada(visitante) {
    return await fetch('/api/websocket/entrada', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(visitante)
    });
  }

  async registrarSalida(visitante) {
    return await fetch('/api/websocket/salida', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(visitante)
    });
  }
}

// Uso:
const manager = new VisitantesManager();
```

## Configuración del Servidor

### Variables de Entorno
```env
BROADCAST_DRIVER=reverb
REVERB_APP_KEY=local-app-key
REVERB_APP_SECRET=local-app-secret
REVERB_APP_ID=local-app-id
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8081
REVERB_HOST=localhost
REVERB_PORT=8081
REVERB_SCHEME=http
```

### Iniciar Servidor WebSocket
```bash
# En entorno Docker
docker exec -it cdattg_app php artisan reverb:start --host=0.0.0.0 --port=8081

# Verificar funcionamiento
curl http://localhost:8081
```

## Pruebas Rápidas

### Probar Endpoints HTTP
```bash
# Obtener estadísticas
curl http://localhost/api/websocket/estadisticas

# Registrar entrada de prueba
curl -X POST http://localhost/api/websocket/entrada \
  -H "Content-Type: application/json" \
  -d '{
    "persona_id": 999,
    "nombre": "Usuario Prueba",
    "documento": "99999999",
    "rol": "Instructor"
  }'
```

### Probar WebSocket
```bash
# Instalar wscat
npm install -g wscat

# Conectar manualmente
wscat -c ws://localhost:8081/app/local-app-key

# Suscribirse a canales
{"event":"pusher:subscribe","data":{"channel":"visitantes"}}
{"event":"pusher:subscribe","data":{"channel":"estadisticas-visitantes"}}
```

## Solución de Problemas

### WebSocket no se conecta
- Verificar que Reverb esté ejecutándose en puerto 8081
- Verificar configuración de firewall
- Probar con wscat para diagnóstico

### Endpoints devuelven error
- Verificar que la aplicación Laravel esté ejecutándose
- Revisar logs en `storage/logs/laravel.log`
- Probar endpoint de prueba: `curl http://localhost/api/test`

### Eventos no se reciben
- Verificar suscripción a canales correctos
- Confirmar nombres de eventos
- Revisar configuración de broadcasting

## Consideraciones para Producción

- Configurar SSL/TLS para WebSockets seguros
- Usar autenticación para WebSockets si es necesario
- Implementar reconexión automática en el cliente
- Monitorear rendimiento del servidor WebSocket

---

**Nota**: Esta guía se basa en la implementación actual del sistema. Para cambios en la configuración o nuevos endpoints, consultar el código fuente en los archivos correspondientes.
