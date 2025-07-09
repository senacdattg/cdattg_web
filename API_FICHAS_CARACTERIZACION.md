# API de Fichas de Caracterización

Esta documentación describe los endpoints disponibles para obtener información de las fichas de caracterización del sistema.

## Autenticación

Todos los endpoints requieren autenticación mediante Sanctum. Incluye el token de autenticación en el header de la petición:

```
Authorization: Bearer {token}
```

## Endpoints Disponibles

### 1. Obtener Todas las Fichas de Caracterización

**URL:** `GET /api/fichas-caracterizacion/all`

**Descripción:** Obtiene todas las fichas de caracterización activas con su información completa.

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Fichas de caracterización obtenidas exitosamente",
    "data": [
        {
            "id": 1,
            "numero_ficha": "2923560",
            "fecha_inicio": "2024-04-15",
            "fecha_fin": "2026-07-14",
            "total_horas": 3345,
            "status": true,
            "programa_formacion": {
                "id": 1,
                "nombre": "Técnico en Programación de Software",
                "codigo": "228106",
                "nivel_formacion": "Técnico"
            },
            "instructor_principal": {
                "id": 1,
                "persona": {
                    "id": 1,
                    "primer_nombre": "Juan",
                    "segundo_nombre": "",
                    "primer_apellido": "Pérez",
                    "segundo_apellido": "García",
                    "tipo_documento": "CC",
                    "numero_documento": "12345678",
                    "email": "juan.perez@email.com",
                    "telefono": "3001234567"
                }
            },
            "jornada_formacion": {
                "id": 1,
                "jornada": "Mañana"
            },
            "ambiente": {
                "id": 60,
                "nombre": "MODELO-B3-P3-PLM CAD Y ROBÓTICA",
                "piso": {
                    "id": 10,
                    "piso": "P3",
                    "bloque": {
                        "id": 5,
                        "nombre": "B3"
                    }
                }
            },
            "modalidad_formacion": {
                "id": 18,
                "nombre": "Presencial"
            },
            "sede": {
                "id": 2,
                "sede": "MODELO",
                "direccion": "Cra 19c no. 16-48"
            },
            "dias_formacion": [
                {
                    "id": 1,
                    "hora_inicio": "08:00:00",
                    "hora_fin": "12:00:00",
                    "dia": {
                        "id": 1,
                        "nombre": "Lunes"
                    }
                }
            ],
            "instructores_asignados": [
                {
                    "id": 1,
                    "fecha_inicio": "2024-04-15",
                    "fecha_fin": "2026-07-14",
                    "total_horas_ficha": 3345,
                    "instructor": {
                        "id": 1,
                        "persona": {
                            "id": 1,
                            "primer_nombre": "Juan",
                            "segundo_nombre": "",
                            "primer_apellido": "Pérez",
                            "segundo_apellido": "García",
                            "tipo_documento": "CC",
                            "numero_documento": "12345678",
                            "email": "juan.perez@email.com",
                            "telefono": "3001234567"
                        }
                    }
                }
            ],
            "created_at": "2024-03-20T09:12:24.000000Z",
            "updated_at": "2024-03-20T09:12:24.000000Z"
        }
    ],
    "total": 1
}
```

### 2. Obtener Ficha de Caracterización por ID

**URL:** `GET /api/fichas-caracterizacion/{id}`

**Descripción:** Obtiene una ficha de caracterización específica por su ID.

**Parámetros:**
- `id` (integer): ID de la ficha de caracterización

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Ficha de caracterización obtenida exitosamente",
    "data": {
        // Misma estructura que el endpoint anterior, pero con una sola ficha
    }
}
```

**Respuesta de Error (404):**
```json
{
    "success": false,
    "message": "Ficha de caracterización no encontrada"
}
```

### 3. Buscar Fichas de Caracterización por Número

**URL:** `POST /api/fichas-caracterizacion/search`

**Descripción:** Busca fichas de caracterización por número de ficha (búsqueda parcial).

**Parámetros:**
```json
{
    "numero_ficha": "2923560"
}
```

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Fichas de caracterización encontradas exitosamente",
    "data": [
        // Array con las fichas encontradas
    ],
    "total": 1
}
```

**Respuesta de Error (404):**
```json
{
    "success": false,
    "message": "No se encontraron fichas de caracterización con el número proporcionado",
    "data": []
}
```

## Estructura de Datos

### Ficha de Caracterización
- `id`: ID único de la ficha
- `numero_ficha`: Número de la ficha
- `fecha_inicio`: Fecha de inicio de la formación
- `fecha_fin`: Fecha de fin de la formación
- `total_horas`: Total de horas de la formación
- `status`: Estado de la ficha (activa/inactiva)

### Programa de Formación
- `id`: ID del programa
- `nombre`: Nombre del programa
- `codigo`: Código del programa
- `nivel_formacion`: Nivel de formación

### Instructor Principal
- `id`: ID del instructor
- `persona`: Información personal del instructor

### Ambiente
- `id`: ID del ambiente
- `nombre`: Nombre del ambiente
- `piso`: Información del piso y bloque

### Sede
- `id`: ID de la sede
- `sede`: Nombre de la sede
- `direccion`: Dirección de la sede

### Días de Formación
- `id`: ID del día de formación
- `hora_inicio`: Hora de inicio
- `hora_fin`: Hora de fin
- `dia`: Información del día

### Instructores Asignados
- `id`: ID de la asignación
- `fecha_inicio`: Fecha de inicio de la asignación
- `fecha_fin`: Fecha de fin de la asignación
- `total_horas_ficha`: Total de horas asignadas
- `instructor`: Información del instructor asignado

## Códigos de Estado HTTP

- `200`: Petición exitosa
- `404`: Recurso no encontrado
- `500`: Error interno del servidor

## Ejemplos de Uso

### Obtener todas las fichas
```bash
curl -X GET "http://localhost:8000/api/fichas-caracterizacion/all" \
  -H "Authorization: Bearer {token}"
```

### Obtener ficha específica
```bash
curl -X GET "http://localhost:8000/api/fichas-caracterizacion/1" \
  -H "Authorization: Bearer {token}"
```

### Buscar por número de ficha
```bash
curl -X POST "http://localhost:8000/api/fichas-caracterizacion/search" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"numero_ficha": "2923560"}'
```

## Notas Importantes

1. Todos los endpoints requieren autenticación válida
2. Solo se devuelven fichas con `status = true` (activas)
3. Los campos que no tienen valor se muestran como `null` o `"N/A"`
4. Las fechas se devuelven en formato ISO 8601
5. La búsqueda por número de ficha es parcial (usa LIKE) 
