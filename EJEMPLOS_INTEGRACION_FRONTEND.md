# 🎨 Ejemplos de Integración Frontend - Sistema de Asistencias WebSocket

Este documento muestra cómo integrar el sistema de asistencias con diferentes frameworks frontend.

---

## 📦 Instalación de Dependencias

### Para Laravel + Vue/React
```bash
npm install --save laravel-echo pusher-js
```

---

## 🔵 Vue.js 3 (Composition API)

### 1. Configurar Echo

```javascript
// resources/js/echo-config.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

export default window.Echo;
```

### 2. Componente de Asistencias

```vue
<template>
  <div class="asistencias-container">
    <!-- Header -->
    <div class="header">
      <h1>📋 Asistencias en Tiempo Real</h1>
      <span :class="['status', connected ? 'conectado' : 'desconectado']">
        {{ connected ? '✅ Conectado' : '⚠️ Desconectado' }}
      </span>
    </div>

    <!-- Formulario de Registro -->
    <div class="form-panel">
      <h2>Registrar Asistencia</h2>
      <div class="form-group">
        <label>ID Instructor Ficha:</label>
        <input v-model.number="form.instructor_ficha_id" type="number" />
      </div>
      <div class="form-group">
        <label>ID Aprendiz Ficha:</label>
        <input v-model.number="form.aprendiz_ficha_id" type="number" />
      </div>
      <button @click="registrarEntrada" class="btn btn-primary">
        🚪 Registrar Entrada
      </button>
      <button @click="registrarSalida" class="btn btn-success">
        👋 Registrar Salida
      </button>
    </div>

    <!-- Filtros -->
    <div class="filters">
      <select v-model="filtroJornada" @change="cargarAsistencias">
        <option value="">Todas las jornadas</option>
        <option v-for="jornada in jornadas" :key="jornada.id" :value="jornada.id">
          {{ jornada.nombre }}
        </option>
      </select>
      <input v-model="filtroFecha" type="date" @change="cargarAsistencias" />
    </div>

    <!-- Lista de Asistencias -->
    <div class="asistencias-list">
      <div v-if="asistencias.length === 0" class="empty">
        No hay asistencias registradas
      </div>
      <TransitionGroup name="list" tag="div">
        <div
          v-for="asistencia in asistencias"
          :key="asistencia.id"
          :class="['asistencia-item', asistencia.estado]"
        >
          <strong>{{ asistencia.aprendiz }}</strong>
          <span :class="['badge', asistencia.estado]">
            {{ asistencia.estado.toUpperCase() }}
          </span>
          <small>
            {{ asistencia.jornada }} | Ficha: {{ asistencia.ficha }}
          </small>
          <br />
          <small>
            Entrada: {{ asistencia.hora_ingreso }}
            <span v-if="asistencia.hora_salida">
              | Salida: {{ asistencia.hora_salida }}
            </span>
          </small>
        </div>
      </TransitionGroup>
    </div>

    <!-- Notificaciones -->
    <Transition name="fade">
      <div v-if="notification" class="notification">
        <strong>{{ notification.title }}</strong>
        <p>{{ notification.message }}</p>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import Echo from '@/echo-config';

// Estado
const connected = ref(false);
const asistencias = ref([]);
const jornadas = ref([
  { id: 1, nombre: 'Mañana' },
  { id: 2, nombre: 'Tarde' },
  { id: 3, nombre: 'Noche' },
]);
const filtroJornada = ref('');
const filtroFecha = ref(new Date().toISOString().split('T')[0]);
const notification = ref(null);
const form = ref({
  instructor_ficha_id: 1,
  aprendiz_ficha_id: 1,
});

// Métodos
const registrarEntrada = async () => {
  try {
    const response = await axios.post('/api/asistencia/entrada', form.value);
    mostrarNotificacion('Entrada Registrada', response.data.message);
  } catch (error) {
    mostrarNotificacion('Error', error.response?.data?.message || 'Error al registrar');
  }
};

const registrarSalida = async () => {
  try {
    const response = await axios.post('/api/asistencia/salida', {
      aprendiz_ficha_id: form.value.aprendiz_ficha_id,
    });
    mostrarNotificacion('Salida Registrada', response.data.message);
  } catch (error) {
    mostrarNotificacion('Error', error.response?.data?.message || 'Error al registrar');
  }
};

const cargarAsistencias = async () => {
  try {
    const params = new URLSearchParams();
    if (filtroJornada.value) params.append('jornada_id', filtroJornada.value);
    if (filtroFecha.value) params.append('fecha', filtroFecha.value);

    const response = await axios.get(`/api/asistencia/jornada?${params}`);
    asistencias.value = response.data.asistencias;
  } catch (error) {
    console.error('Error al cargar asistencias:', error);
  }
};

const mostrarNotificacion = (title, message) => {
  notification.value = { title, message };
  setTimeout(() => {
    notification.value = null;
  }, 3000);
};

// WebSocket
let channel;

onMounted(() => {
  // Cargar asistencias iniciales
  cargarAsistencias();

  // Conectar a WebSocket
  channel = Echo.channel('asistencias');

  channel.listen('.NuevaAsistenciaRegistrada', (data) => {
    console.log('Nueva asistencia:', data);

    // Agregar al inicio de la lista
    asistencias.value.unshift({
      id: data.id,
      aprendiz: data.aprendiz,
      estado: data.estado,
      jornada: data.jornada,
      ficha: data.ficha,
      hora_ingreso: new Date(data.timestamp).toLocaleTimeString(),
      hora_salida: data.estado === 'salida' ? new Date(data.timestamp).toLocaleTimeString() : null,
    });

    // Mostrar notificación
    mostrarNotificacion(
      `${data.estado === 'entrada' ? '🚪 Entrada' : '👋 Salida'}`,
      `${data.aprendiz} - ${data.jornada}`
    );
  });

  // Estado de conexión
  Echo.connector.pusher.connection.bind('connected', () => {
    connected.value = true;
  });

  Echo.connector.pusher.connection.bind('disconnected', () => {
    connected.value = false;
  });
});

onUnmounted(() => {
  if (channel) {
    Echo.leave('asistencias');
  }
});
</script>

<style scoped>
.asistencias-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.status {
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 14px;
}

.status.conectado {
  background: #10b981;
  color: white;
}

.status.desconectado {
  background: #ef4444;
  color: white;
}

.form-panel {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 2px solid #e5e7eb;
  border-radius: 5px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  margin-right: 10px;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-success {
  background: #10b981;
  color: white;
}

.asistencias-list {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.asistencia-item {
  background: #f9fafb;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 10px;
  border-left: 4px solid #667eea;
}

.asistencia-item.entrada {
  border-left-color: #10b981;
}

.asistencia-item.salida {
  border-left-color: #ef4444;
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: bold;
  margin-left: 10px;
}

.badge.entrada {
  background: #d1fae5;
  color: #065f46;
}

.badge.salida {
  background: #fee2e2;
  color: #991b1b;
}

.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  max-width: 350px;
  z-index: 1000;
}

/* Transiciones */
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}

.list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
```

---

## ⚛️ React (con Hooks)

### 1. Hook Personalizado para Echo

```javascript
// hooks/useEcho.js
import { useEffect, useState } from 'react';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

export const useEcho = (channelName, eventName, callback) => {
    const [connected, setConnected] = useState(false);

    useEffect(() => {
        const channel = echo.channel(channelName);
        
        channel.listen(`.${eventName}`, callback);

        echo.connector.pusher.connection.bind('connected', () => {
            setConnected(true);
        });

        echo.connector.pusher.connection.bind('disconnected', () => {
            setConnected(false);
        });

        return () => {
            echo.leave(channelName);
        };
    }, [channelName, eventName, callback]);

    return { connected };
};
```

### 2. Componente de Asistencias

```jsx
// components/AsistenciasRealTime.jsx
import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { useEcho } from '../hooks/useEcho';
import './AsistenciasRealTime.css';

const AsistenciasRealTime = () => {
    const [asistencias, setAsistencias] = useState([]);
    const [form, setForm] = useState({
        instructor_ficha_id: 1,
        aprendiz_ficha_id: 1,
    });
    const [notification, setNotification] = useState(null);

    // WebSocket
    const handleNuevaAsistencia = useCallback((data) => {
        console.log('Nueva asistencia:', data);

        // Agregar al inicio
        setAsistencias((prev) => [{
            id: data.id,
            aprendiz: data.aprendiz,
            estado: data.estado,
            jornada: data.jornada,
            ficha: data.ficha,
            hora_ingreso: new Date(data.timestamp).toLocaleTimeString(),
            hora_salida: data.estado === 'salida' ? new Date(data.timestamp).toLocaleTimeString() : null,
        }, ...prev]);

        // Mostrar notificación
        mostrarNotificacion(
            `${data.estado === 'entrada' ? '🚪 Entrada' : '👋 Salida'}`,
            `${data.aprendiz} - ${data.jornada}`
        );
    }, []);

    const { connected } = useEcho('asistencias', 'NuevaAsistenciaRegistrada', handleNuevaAsistencia);

    // Cargar asistencias iniciales
    useEffect(() => {
        cargarAsistencias();
    }, []);

    const cargarAsistencias = async () => {
        try {
            const response = await axios.get('/api/asistencia/jornada');
            setAsistencias(response.data.asistencias);
        } catch (error) {
            console.error('Error al cargar asistencias:', error);
        }
    };

    const registrarEntrada = async () => {
        try {
            const response = await axios.post('/api/asistencia/entrada', form);
            mostrarNotificacion('Entrada Registrada', response.data.message);
        } catch (error) {
            mostrarNotificacion('Error', error.response?.data?.message || 'Error al registrar');
        }
    };

    const registrarSalida = async () => {
        try {
            const response = await axios.post('/api/asistencia/salida', {
                aprendiz_ficha_id: form.aprendiz_ficha_id,
            });
            mostrarNotificacion('Salida Registrada', response.data.message);
        } catch (error) {
            mostrarNotificacion('Error', error.response?.data?.message || 'Error al registrar');
        }
    };

    const mostrarNotificacion = (title, message) => {
        setNotification({ title, message });
        setTimeout(() => {
            setNotification(null);
        }, 3000);
    };

    return (
        <div className="asistencias-container">
            {/* Header */}
            <div className="header">
                <h1>📋 Asistencias en Tiempo Real</h1>
                <span className={`status ${connected ? 'conectado' : 'desconectado'}`}>
                    {connected ? '✅ Conectado' : '⚠️ Desconectado'}
                </span>
            </div>

            {/* Formulario */}
            <div className="form-panel">
                <h2>Registrar Asistencia</h2>
                <div className="form-group">
                    <label>ID Instructor Ficha:</label>
                    <input
                        type="number"
                        value={form.instructor_ficha_id}
                        onChange={(e) => setForm({ ...form, instructor_ficha_id: parseInt(e.target.value) })}
                    />
                </div>
                <div className="form-group">
                    <label>ID Aprendiz Ficha:</label>
                    <input
                        type="number"
                        value={form.aprendiz_ficha_id}
                        onChange={(e) => setForm({ ...form, aprendiz_ficha_id: parseInt(e.target.value) })}
                    />
                </div>
                <button onClick={registrarEntrada} className="btn btn-primary">
                    🚪 Registrar Entrada
                </button>
                <button onClick={registrarSalida} className="btn btn-success">
                    👋 Registrar Salida
                </button>
            </div>

            {/* Lista de Asistencias */}
            <div className="asistencias-list">
                {asistencias.length === 0 ? (
                    <p className="empty">No hay asistencias registradas</p>
                ) : (
                    asistencias.map((asistencia) => (
                        <div key={asistencia.id} className={`asistencia-item ${asistencia.estado}`}>
                            <strong>{asistencia.aprendiz}</strong>
                            <span className={`badge ${asistencia.estado}`}>
                                {asistencia.estado.toUpperCase()}
                            </span>
                            <small>
                                {asistencia.jornada} | Ficha: {asistencia.ficha}
                            </small>
                            <br />
                            <small>
                                Entrada: {asistencia.hora_ingreso}
                                {asistencia.hora_salida && ` | Salida: ${asistencia.hora_salida}`}
                            </small>
                        </div>
                    ))
                )}
            </div>

            {/* Notificación */}
            {notification && (
                <div className="notification">
                    <strong>{notification.title}</strong>
                    <p>{notification.message}</p>
                </div>
            )}
        </div>
    );
};

export default AsistenciasRealTime;
```

---

## 📱 Ejemplo con Vanilla JavaScript

```javascript
// Conectar a WebSocket
const pusher = new Pusher('local', {
    wsHost: '127.0.0.1',
    wsPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss']
});

const channel = pusher.subscribe('asistencias');

channel.bind('NuevaAsistenciaRegistrada', function(data) {
    console.log('Nueva asistencia:', data);
    agregarAsistenciaADOM(data);
});

function agregarAsistenciaADOM(data) {
    const lista = document.getElementById('asistencias-list');
    const item = document.createElement('div');
    item.className = `asistencia-item ${data.estado}`;
    item.innerHTML = `
        <strong>${data.aprendiz}</strong>
        <span class="badge ${data.estado}">${data.estado.toUpperCase()}</span>
        <small>${data.jornada} - Ficha ${data.ficha}</small>
    `;
    lista.insertBefore(item, lista.firstChild);
}
```

---

## 🌐 Variables de Entorno

Asegúrate de tener en tu `.env`:

```env
VITE_REVERB_APP_KEY=local
VITE_REVERB_HOST=127.0.0.1
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

---

**¡Listo para integrar en tu frontend! 🚀**
