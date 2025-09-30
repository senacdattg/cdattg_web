# 🌐 Configuración de Red Local para Desarrollo

## Resumen de Configuración

### 🚀 Servidores Configurados

Ambos servidores están configurados para ser accesibles desde cualquier dispositivo en tu red local:

#### 1. Servidor Laravel
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
- **Acceso local**: http://localhost:8000
- **Acceso desde red**: http://TU_IP:8000 (ej: http://192.168.1.100:8000)

#### 2. Servidor Reverb (WebSockets)
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```
- **Acceso local**: ws://localhost:8080
- **Acceso desde red**: ws://TU_IP:8080 (ej: ws://192.168.1.100:8080)

---

## 📱 Configuración para Dispositivos Móviles

### Paso 1: Obtener tu IP Local

**En Windows:**
```bash
ipconfig
```
Busca "Dirección IPv4" en tu adaptador de red WiFi o Ethernet.  
Ejemplo: `192.168.1.100`

**En Linux/Mac:**
```bash
hostname -I
# o
ifconfig
```

### Paso 2: Configurar Flutter App

Actualiza tu configuración de WebSocket en Flutter con tu IP local:

```dart
await PusherChannelsFlutter.init(
  apiKey: "local",
  cluster: "mt1",
  hostEndPoint: "192.168.1.100", // ← Tu IP aquí
  port: 8080,
  encrypted: false,
);
```

### Paso 3: Configurar URLs de API

En tu app Flutter, usa la IP de tu máquina para las llamadas API:

```dart
// Base URL para desarrollo
const String baseUrl = "http://192.168.1.100:8000/api";

// Ejemplo de uso
final response = await http.get(Uri.parse('$baseUrl/asistencia'));
```

---

## 🔒 Configuración de Firewall

### Windows Firewall

Si no puedes acceder desde dispositivos móviles, asegúrate de permitir las conexiones:

1. **Puerto 8000** (Laravel)
   ```powershell
   netsh advfirewall firewall add rule name="Laravel Server" dir=in action=allow protocol=TCP localport=8000
   ```

2. **Puerto 8080** (Reverb)
   ```powershell
   netsh advfirewall firewall add rule name="Laravel Reverb" dir=in action=allow protocol=TCP localport=8080
   ```

### Linux Firewall (ufw)

```bash
sudo ufw allow 8000/tcp
sudo ufw allow 8080/tcp
sudo ufw reload
```

---

## 🧪 Verificar Conectividad

### Desde tu PC

```bash
# Verificar Laravel
curl http://localhost:8000

# Verificar Reverb está escuchando
netstat -an | findstr :8080
```

### Desde tu dispositivo móvil

1. **Abre el navegador** en tu móvil
2. **Accede a**: http://TU_IP:8000
3. **Deberías ver** la aplicación Laravel

---

## 📋 Variables de Entorno Recomendadas

Actualiza tu archivo `.env`:

```env
# Servidor Laravel
APP_URL=http://192.168.1.100:8000

# Broadcasting - Reverb
BROADCAST_DRIVER=reverb

# Configuración del servidor Reverb
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Configuración de la aplicación Reverb
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local

# Host público (tu IP local)
REVERB_HOST=192.168.1.100
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

## ⚠️ Notas Importantes

1. **IP Dinámica**: La IP de tu máquina puede cambiar. Si no puedes conectarte, verifica tu IP nuevamente.

2. **Misma Red**: Asegúrate de que tu dispositivo móvil y tu PC estén en la **misma red WiFi**.

3. **VPN**: Si usas VPN, puede interferir con la conectividad local. Desactívala para desarrollo.

4. **Antivirus**: Algunos antivirus bloquean conexiones entrantes. Agrega excepciones si es necesario.

5. **CORS**: Para producción, configura CORS correctamente en `config/cors.php`:
   ```php
   'allowed_origins' => ['http://192.168.1.100:8000'],
   ```

---

## 🔄 Comandos de Inicio Completos

### Terminal 1: Laravel
```bash
cd C:\dev\cdattg_asistence_web
php artisan serve --host=0.0.0.0 --port=8000
```

### Terminal 2: Reverb
```bash
cd C:\dev\cdattg_asistence_web
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug
```

### Terminal 3: Queue Worker
```bash
cd C:\dev\cdattg_asistence_web
php artisan queue:work
```

---

## ✅ Checklist de Verificación

- [ ] IP local obtenida
- [ ] Servidor Laravel iniciado con `--host=0.0.0.0`
- [ ] Servidor Reverb iniciado con `--host=0.0.0.0`
- [ ] Firewall configurado (puertos 8000 y 8080 abiertos)
- [ ] Dispositivos en la misma red WiFi
- [ ] Variables de entorno actualizadas
- [ ] App Flutter configurada con IP correcta
- [ ] Conectividad verificada desde navegador móvil

---

**¡Listo para desarrollar con dispositivos móviles en red local!** 🎉
