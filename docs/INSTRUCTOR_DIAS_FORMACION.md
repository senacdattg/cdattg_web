# Gestión de Días de Formación de Instructores

## 📋 Descripción General

Este módulo permite asignar días de formación específicos a cada instructor dentro de una ficha de caracterización. Los "días de formación" representan los días de la semana (lunes a domingo) en los que el instructor dictará formación, no cantidad de días.

## 🎯 Funcionalidades Implementadas

### 1. Asignación de Días de la Semana

- Seleccionar días específicos (lunes, martes, miércoles, etc.)
- Definir horarios para cada día (hora inicio y fin)
- El sistema valida automáticamente la disponibilidad del instructor

### 2. Validación de Disponibilidad

El sistema verifica:
- ✅ Que el instructor esté disponible en los días seleccionados
- ✅ Que no tenga otra ficha asignada en el mismo horario
- ⚠️ Muestra conflictos de horario si existen

### 3. Generación Automática de Fechas

- El sistema calcula las fechas reales basándose en:
  - Rango de fechas de la ficha (fecha inicio - fecha fin)
  - Días de la semana seleccionados
- Genera automáticamente todas las sesiones de formación

### 4. Preview de Fechas

- Vista previa de las fechas efectivas antes de guardar
- Muestra el total de sesiones programadas
- Permite verificar que las fechas sean correctas

## 🛠️ Componentes Técnicos

### Modelos

- **`InstructorFichaDias`**: Relación entre instructor-ficha y días de la semana
- **`InstructorFichaCaracterizacion`**: Relación entre instructor y ficha
- **`FichaDiasFormacion`**: Días de formación de la ficha

### Servicio Principal

**`InstructorFichaDiasService`** - Ubicación: `app/Services/InstructorFichaDiasService.php`

#### Métodos Principales:

```php
// Asignar días a un instructor
asignarDiasInstructor(int $instructorFichaId, array $diasData): array

// Validar disponibilidad
validarDisponibilidadInstructor(InstructorFichaCaracterizacion $instructorFicha, array $diasData): array

// Generar fechas efectivas
generarFechasEfectivas(InstructorFichaCaracterizacion $instructorFicha, array $diasData): array

// Verificar disponibilidad de un instructor
estaDisponible(int $instructorId, int $diaId, ?string $horaInicio, ?string $horaFin, ?int $excludeInstructorFichaId): bool
```

### Controlador

**`InstructorFichaDiasController`** - Ubicación: `app/Http/Controllers/InstructorFichaDiasController.php`

### Rutas

**Archivo:** `routes/instructor_ficha_dias/web_instructor_dias.php`

```php
GET  /instructor-ficha/{instructorFichaId}/dias/create     - Formulario de asignación
POST /instructor-ficha/{instructorFichaId}/dias            - Guardar asignación
GET  /instructor-ficha/{instructorFichaId}/dias            - Obtener días asignados
DELETE /instructor-ficha/{instructorFichaId}/dias          - Eliminar asignación
POST /instructor-ficha/{instructorFichaId}/dias/preview-fechas - Preview de fechas
POST /instructor-ficha/verificar-disponibilidad            - Verificar disponibilidad
```

## 📊 Estructura de Datos

### Tabla: `instructor_ficha_dias`

```sql
id                  BIGINT (PK)
instructor_ficha_id BIGINT (FK -> instructor_fichas_caracterizacion)
dia_id              BIGINT (FK -> parametros_temas)
hora_inicio         TIME (nullable)
hora_fin            TIME (nullable)
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Mapeo de Días

Los días se almacenan como parámetros con IDs:

- **12** = Lunes
- **13** = Martes
- **14** = Miércoles
- **15** = Jueves
- **16** = Viernes
- **17** = Sábado
- **18** = Domingo

## 🔧 Uso del Sistema

### 1. Asignar Días a un Instructor

**Vía Interfaz Web:**

1. Navegar a la ficha de caracterización
2. Seleccionar el instructor
3. Click en "Asignar Días de Formación"
4. Seleccionar los días de la semana
5. Definir horarios (opcional)
6. Click en "Ver Fechas Efectivas" para preview
7. Click en "Guardar Asignación"

**Vía API/Código:**

```php
use App\Services\InstructorFichaDiasService;

$diasService = app(InstructorFichaDiasService::class);

$diasData = [
    ['dia_id' => 12, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], // Lunes
    ['dia_id' => 14, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], // Miércoles
    ['dia_id' => 16, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], // Viernes
];

$resultado = $diasService->asignarDiasInstructor($instructorFichaId, $diasData);

if ($resultado['success']) {
    echo "Total sesiones: " . $resultado['total_sesiones'];
    print_r($resultado['fechas_efectivas']);
} else {
    print_r($resultado['conflictos']);
}
```

### 2. Validar Disponibilidad

```php
$instructorFicha = InstructorFichaCaracterizacion::find($id);
$validacion = $diasService->validarDisponibilidadInstructor($instructorFicha, $diasData);

if (!$validacion['disponible']) {
    foreach ($validacion['conflictos'] as $conflicto) {
        echo "{$conflicto['dia_nombre']}: Conflicto con ficha {$conflicto['ficha_conflicto']}";
    }
}
```

### 3. Generar Preview de Fechas

```php
$fechas = $diasService->generarFechasEfectivas($instructorFicha, $diasData);

foreach ($fechas as $fecha) {
    echo "{$fecha['fecha']} - {$fecha['dia_semana']} ({$fecha['hora_inicio']} - {$fecha['hora_fin']})";
}
```

## 📝 Ejemplo Práctico Completo

### Escenario:
- **Ficha:** 2923560
- **Programa:** Análisis y Desarrollo de Software
- **Fechas:** 21 oct – 15 nov 2025
- **Instructor:** Carlos Gómez
- **Días seleccionados:** Lunes, Miércoles, Viernes
- **Horario:** 08:00 - 12:00

### Resultado Esperado:

El sistema generará automáticamente las siguientes fechas:

```
22/10/2025 - Miércoles - 08:00 a 12:00
24/10/2025 - Viernes   - 08:00 a 12:00
27/10/2025 - Lunes     - 08:00 a 12:00
29/10/2025 - Miércoles - 08:00 a 12:00
31/10/2025 - Viernes   - 08:00 a 12:00
03/11/2025 - Lunes     - 08:00 a 12:00
05/11/2025 - Miércoles - 08:00 a 12:00
07/11/2025 - Viernes   - 08:00 a 12:00
10/11/2025 - Lunes     - 08:00 a 12:00
12/11/2025 - Miércoles - 08:00 a 12:00
14/11/2025 - Viernes   - 08:00 a 12:00
```

**Total:** 11 sesiones de formación

### Código:

```php
$instructorFichaId = 1; // ID de la relación instructor-ficha

$diasData = [
    ['dia_id' => 12, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], // Lunes
    ['dia_id' => 14, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], // Miércoles
    ['dia_id' => 16, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], // Viernes
];

$resultado = $diasService->asignarDiasInstructor($instructorFichaId, $diasData);

// Output:
// success: true
// total_sesiones: 11
// fechas_efectivas: [array de 11 fechas]
```

## ⚠️ Validaciones Implementadas

1. **Validación de días mínimos:** Al menos 1 día debe ser seleccionado
2. **Validación de horarios:** Hora fin debe ser posterior a hora inicio
3. **Validación de conflictos:** No puede asignar si hay conflicto horario
4. **Validación de existencia:** Instructor y ficha deben existir
5. **Validación de formato:** Horarios deben tener formato HH:MM

## 🔐 Permisos y Seguridad

El módulo requiere autenticación (`auth` middleware). Considera agregar permisos específicos:

```php
'GESTIONAR DIAS INSTRUCTOR'
'VER DIAS INSTRUCTOR'
'ASIGNAR DIAS INSTRUCTOR'
'ELIMINAR DIAS INSTRUCTOR'
```

## 🚀 Próximas Mejoras (Opcional)

- [ ] Notificaciones al instructor cuando se asignan días
- [ ] Calendario visual de disponibilidad
- [ ] Exportar calendario a iCal/Google Calendar
- [ ] Reportes de carga horaria por instructor
- [ ] Validación de horas máximas por semana
- [ ] Gestión de excepciones (días festivos, vacaciones)

## 📞 Soporte

Para preguntas o problemas, revisar los logs en:
- `storage/logs/laravel.log`

Los registros incluyen información detallada de:
- Asignaciones exitosas
- Conflictos detectados
- Errores en validaciones

