# 📚 Documentación API - Sistema de Asistencia SENA

## 🔐 Autenticación

Todas las rutas API requieren autenticación mediante sesión de Laravel.

### Headers requeridos:
```http
Accept: application/json
Content-Type: application/json
X-CSRF-TOKEN: {token}
```

---

## 📋 Endpoints - Aprendices

### **GET** `/api/aprendices`
Lista todos los aprendices

#### Response:
```json
{
  "success": true,
  "aprendices": [
    {
      "id": 1,
      "persona": {
        "nombre_completo": "Juan Pérez",
        "numero_documento": "123456789",
        "email": "juan@example.com"
      },
      "ficha": {
        "numero": "2089876",
        "programa": "ADSI"
      },
      "estado": "Activo"
    }
  ]
}
```

### **GET** `/api/aprendices/search?q={termino}`
Busca aprendices por nombre o documento

#### Parámetros:
- `q` (string, requerido): Término de búsqueda

#### Response:
```json
{
  "success": true,
  "aprendices": [...]
}
```

### **GET** `/api/aprendices/{id}`
Obtiene detalles de un aprendiz

#### Response:
```json
{
  "id": 1,
  "persona": {...},
  "ficha": {...},
  "estado": true,
  "created_at": "2024-01-01 10:00:00"
}
```

### **POST** `/api/aprendices`
Crea un nuevo aprendiz

#### Body:
```json
{
  "persona_id": 1,
  "ficha_caracterizacion_id": 1,
  "estado": true
}
```

### **PUT** `/api/aprendices/{id}`
Actualiza un aprendiz

#### Body:
```json
{
  "persona_id": 1,
  "ficha_caracterizacion_id": 2,
  "estado": true
}
```

### **DELETE** `/api/aprendices/{id}`
Elimina un aprendiz (soft delete)

---

## 📊 Endpoints - Asistencias

### **GET** `/asistencias`
Vista principal de asistencias

### **GET** `/asistencias/ficha`
Obtiene asistencias por ficha

#### Parámetros:
- `ficha` (int, requerido): ID de la ficha

#### Response:
```json
{
  "asistencias": [
    {
      "id": 1,
      "aprendiz": {
        "nombres": "Juan",
        "apellidos": "Pérez",
        "numero_identificacion": "123456789"
      },
      "horarios": {
        "ingreso": "08:00:00",
        "salida": "12:00:00"
      },
      "novedades": {
        "entrada": "Puntual",
        "salida": "Normal"
      }
    }
  ]
}
```

### **GET** `/asistencias/fecha`
Obtiene asistencias por fecha

#### Parámetros:
- `ficha` (int, requerido): ID de la ficha
- `fecha_inicio` (date, requerido): Fecha inicial (Y-m-d)
- `fecha_fin` (date, requerido): Fecha final (Y-m-d)

### **GET** `/asistencias/documento`
Obtiene asistencias por documento

#### Parámetros:
- `documento` (string, requerido): Número de documento

### **POST** `/asistencias`
Registra asistencia(s)

#### Body (Individual):
```json
{
  "caracterizacion_id": 1,
  "nombres": "Juan",
  "apellidos": "Pérez",
  "numero_identificacion": "123456789",
  "hora_ingreso": "2024-01-01 08:00:00"
}
```

#### Body (Lote):
```json
{
  "caracterizacion_id": 1,
  "attendance": [
    {
      "nombres": "Juan",
      "apellidos": "Pérez",
      "numero_identificacion": "123456789",
      "hora_ingreso": "2024-01-01 08:00:00"
    }
  ]
}
```

### **PUT** `/asistencias`
Actualiza hora de salida

#### Body:
```json
{
  "caracterizacion_id": 1,
  "hora_salida": "2024-01-01 12:00:00",
  "fecha": "2024-01-01"
}
```

### **POST** `/asistencias/novedad-entrada`
Actualiza novedad de entrada

#### Body:
```json
{
  "caracterizacion_id": 1,
  "numero_identificacion": "123456789",
  "hora_ingreso": "08:00:00",
  "novedad_entrada": "Tarde"
}
```

### **POST** `/asistencias/novedad-salida`
Actualiza novedad de salida

#### Body:
```json
{
  "caracterizacion_id": 1,
  "numero_identificacion": "123456789",
  "hora_ingreso": "08:00:00",
  "novedad_salida": "Anticipada"
}
```

---

## 👨‍🏫 Endpoints - Instructores

### **GET** `/instructores`
Lista todos los instructores

#### Parámetros opcionales:
- `search` (string): Búsqueda por nombre o documento
- `estado` (string): todos|activos|inactivos
- `especialidad` (string): ID de especialidad
- `regional` (int): ID de regional

### **POST** `/instructores`
Crea un instructor

#### Body:
```json
{
  "persona_id": 1,
  "regional_id": 1,
  "anos_experiencia": 5,
  "experiencia_laboral": "...",
  "especialidades": [1, 2, 3]
}
```

### **GET** `/instructores/{id}/disponibilidad`
Verifica disponibilidad del instructor

#### Parámetros:
- `fecha_inicio` (date, requerido)
- `fecha_fin` (date, requerido)
- `especialidad_requerida` (string, opcional)
- `horas_semanales` (int, opcional)

#### Response:
```json
{
  "success": true,
  "disponibilidad": {
    "disponible": true,
    "razones": [],
    "conflictos": [],
    "carga_actual": 35,
    "horas_disponibles": 13
  }
}
```

---

## ⚙️ Configuración

### **GET** `/api/configuracion/fichas`
Obtiene fichas activas (con caché)

### **GET** `/api/configuracion/regionales`
Obtiene regionales activas (con caché)

### **GET** `/api/configuracion/programas`
Obtiene programas de formación activos (con caché)

---

## 🚦 Rate Limiting

Las APIs tienen límite de:
- **60 requests por minuto** para usuarios autenticados
- **30 requests por minuto** para IPs sin autenticar

### Headers de respuesta:
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
```

---

## 🔄 Códigos de Estado

| Código | Significado |
|--------|-------------|
| 200 | OK - Solicitud exitosa |
| 201 | Created - Recurso creado |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - No autenticado |
| 403 | Forbidden - Sin permisos |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Validación fallida |
| 429 | Too Many Requests - Rate limit excedido |
| 500 | Internal Server Error - Error del servidor |

---

## 📝 Ejemplos de Uso

### cURL - Registrar Asistencia
```bash
curl -X POST https://sena.edu.co/asistencias \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: {token}" \
  -d '{
    "caracterizacion_id": 1,
    "nombres": "Juan",
    "apellidos": "Pérez",
    "numero_identificacion": "123456789",
    "hora_ingreso": "2024-01-01 08:00:00"
  }'
```

### JavaScript - Buscar Aprendices
```javascript
const response = await fetch('/api/aprendices/search?q=Juan', {
  headers: {
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  }
});

const data = await response.json();
console.log(data.aprendices);
```

### jQuery - DataTable con API
```javascript
$('#aprendices-table').DataTable({
  processing: true,
  serverSide: true,
  ajax: '/aprendices/datatable',
  columns: [
    { data: 'documento', name: 'documento' },
    { data: 'nombre', name: 'nombre' },
    { data: 'ficha', name: 'ficha' },
    { data: 'programa', name: 'programa' },
    { data: 'estado', name: 'estado' },
    { data: 'acciones', orderable: false, searchable: false }
  ]
});
```

---

## 🔍 Sistema de Caché

Todas las consultas de configuración están cacheadas:

- **Parámetros**: 24 horas
- **Regionales**: 12 horas
- **Programas**: 6 horas
- **Fichas**: 1 hora
- **Estadísticas**: 15 minutos

### Limpiar caché:
```bash
php artisan cache:clear
php artisan cache:warmup --flush
```

---

## 🧪 Testing

### Ejecutar tests:
```bash
# Todos los tests
php artisan test

# Solo Feature tests
php artisan test --testsuite=Feature

# Solo Unit tests
php artisan test --testsuite=Unit

# Con cobertura
php artisan test --coverage
```

---

## 📞 Soporte

Para más información, contactar al equipo de desarrollo.

