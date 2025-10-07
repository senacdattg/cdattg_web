<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

# 📚 Módulo de Resultados de Aprendizaje (RAP)

Sistema completo de gestión de Resultados de Aprendizaje para el CDATTG - SENA.

## 🎯 Características Principales

### **CRUD Completo**
- ✅ Crear, Leer, Actualizar, Eliminar Resultados de Aprendizaje
- ✅ Validaciones de negocio robustas
- ✅ Gestión de competencias asociadas
- ✅ Control de estados (Activo/Inactivo)

### **Búsqueda Avanzada**
- ✅ Filtros por código, nombre, competencia, estado
- ✅ Filtros por rango de fechas (inicio/fin)
- ✅ Filtros por duración (mín/máx)
- ✅ API JSON para búsqueda AJAX
- ✅ Parámetros persistentes en URL

### **Seguridad y Permisos**
- ✅ 11 permisos específicos configurados
- ✅ Políticas basadas en roles (SUPER ADMIN, ADMIN, INSTRUCTOR)
- ✅ Middleware de autenticación y autorización
- ✅ Logging completo de acciones

### **UI/UX**
- ✅ Diseño moderno con gradientes SENA
- ✅ Responsive design
- ✅ Select2 para selección de competencias
- ✅ SweetAlert2 para confirmaciones
- ✅ Auto-dismiss de alertas

## 📋 Tabla de Contenidos

- [Instalación](#instalación)
- [Modelos y Relaciones](#modelos-y-relaciones)
- [Endpoints de API](#endpoints-de-api)
- [Validaciones](#validaciones)
- [Permisos](#permisos)
- [Tests](#tests)
- [Estructura de Base de Datos](#estructura-de-base-de-datos)

---

## 🚀 Instalación

### **1. Ejecutar Migraciones**

```bash
php artisan migrate
```

Esto creará:
- Tabla `resultados_aprendizajes`
- Tabla `resultados_aprendizaje_competencia` (tabla pivote)
- Campo `status` con valor por defecto
- 7 índices para optimización

### **2. Ejecutar Seeders**

```bash
# Seeder de permisos
php artisan db:seed --class=ResultadosAprendizajePermissionsSeeder

# Seeder de datos de prueba
php artisan db:seed --class=ResultadosAprendizajeSeeder

# O todos los seeders
php artisan db:seed
```

### **3. Compilar Assets**

```bash
npm install
npm run build
```

---

## 🗂️ Modelos y Relaciones

### **Modelo: ResultadosAprendizaje**

```php
namespace App\Models;

class ResultadosAprendizaje extends Model
{
    // Relaciones
    public function competencias()        // BelongsToMany
    public function guiasAprendizaje()   // BelongsToMany
    public function userCreate()         // BelongsTo
    public function userEdit()           // BelongsTo
    
    // Scopes
    scopeActivos($query)
    scopeInactivos($query)
    scopePorCompetencia($query, $competenciaId)
    scopePorCodigo($query, $codigo)
    scopePorFecha($query, $fechaInicio, $fechaFin)
    scopeOrdenadoPorCodigo($query)
    
    // Helpers
    isActivo()                // bool
    duracionEnHoras()         // string
    tieneFechasDefinidas()    // bool
    estaVigente()            // bool
    contarGuiasAsociadas()   // int
    getEstadoFormateadoAttribute()  // string
    getNombreCompletoAttribute()    // string
}
```

### **Relaciones**

```
ResultadosAprendizaje (1) ↔ (N) Competencias
ResultadosAprendizaje (N) ↔ (N) GuiasAprendizaje
ResultadosAprendizaje (N) → (1) User (creador)
ResultadosAprendizaje (N) → (1) User (editor)
```

---

## 🌐 Endpoints de API

### **Web Routes**

| Método | URI | Acción | Permiso |
|--------|-----|--------|---------|
| GET | `/resultados-aprendizaje` | Listar RAPs | VER RESULTADO APRENDIZAJE |
| GET | `/resultados-aprendizaje/create` | Formulario crear | CREAR RESULTADO APRENDIZAJE |
| POST | `/resultados-aprendizaje` | Guardar nuevo | CREAR RESULTADO APRENDIZAJE |
| GET | `/resultados-aprendizaje/{id}` | Ver detalle | VER RESULTADO APRENDIZAJE |
| GET | `/resultados-aprendizaje/{id}/edit` | Formulario editar | EDITAR RESULTADO APRENDIZAJE |
| PUT | `/resultados-aprendizaje/{id}` | Actualizar | EDITAR RESULTADO APRENDIZAJE |
| DELETE | `/resultados-aprendizaje/{id}` | Eliminar | ELIMINAR RESULTADO APRENDIZAJE |

### **API Routes (AJAX)**

#### **Búsqueda General**
```http
GET /resultados-aprendizaje-search?q={término}
```

**Parámetros de búsqueda:**
- `q` - Búsqueda general (código + nombre)
- `codigo` - Filtro por código específico
- `nombre` - Filtro por nombre específico
- `competencia_id` - Filtro por competencia
- `status` - Filtro por estado (0=inactivo, 1=activo)
- `fecha_inicio_desde` - Fecha inicio mínima
- `fecha_inicio_hasta` - Fecha inicio máxima
- `fecha_fin_desde` - Fecha fin mínima
- `fecha_fin_hasta` - Fecha fin máxima
- `duracion_min` - Duración mínima
- `duracion_max` - Duración máxima
- `order_by` - Columna para ordenar (default: 'codigo')
- `order_direction` - Dirección (asc/desc, default: 'asc')
- `per_page` - Resultados por página (default: 10)

**Respuesta:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 100,
    "per_page": 10,
    "current_page": 1,
    "last_page": 10,
    "from": 1,
    "to": 10
  },
  "filters": {...}
}
```

#### **Cambiar Estado**
```http
PUT /resultados-aprendizaje/{id}/cambiar-estado
```

#### **Gestionar Competencias**
```http
GET  /resultados-aprendizaje/{id}/gestionar-competencias
POST /resultados-aprendizaje/{id}/asociar-competencia
DELETE /resultados-aprendizaje/{id}/desasociar-competencia/{competencia_id}
```

---

## ✅ Validaciones

### **Validaciones de Campos**

| Campo | Reglas | Descripción |
|-------|--------|-------------|
| codigo | `required\|string\|max:50\|unique` | Código único |
| nombre | `required\|string\|max:500` | Nombre completo |
| duracion | `required\|numeric\|min:1\|max:9999` | Duración en horas |
| fecha_inicio | `required\|date\|before_or_equal:fecha_fin` | Fecha de inicio |
| fecha_fin | `required\|date\|after_or_equal:fecha_inicio` | Fecha de fin |
| status | `nullable\|boolean` | Estado activo/inactivo |
| competencia_id | `nullable\|exists:competencias,id` | Competencia asociada |

### **Validaciones de Negocio**

1. **Código Único:**
   - No se permite duplicar códigos
   - En actualización se ignora el código actual

2. **Fechas Coherentes:**
   - `fecha_inicio` debe ser anterior o igual a `fecha_fin`
   - Validación bidireccional

3. **Duración Mínima:**
   - Mínimo 1 hora
   - Máximo 9999 horas

4. **Integridad Referencial:**
   - No se puede eliminar RAP con guías asociadas
   - Mensaje descriptivo con cantidad de guías

---

## 🔐 Permisos

### **Lista de Permisos**

```php
'VER RESULTADO APRENDIZAJE'
'CREAR RESULTADO APRENDIZAJE'
'EDITAR RESULTADO APRENDIZAJE'
'ELIMINAR RESULTADO APRENDIZAJE'
'GESTIONAR COMPETENCIAS RAP'
'CAMBIAR ESTADO RAP'
'ASOCIAR GUIA RAP'
'DESASOCIAR GUIA RAP'
'EXPORTAR RAP'
'IMPORTAR RAP'
'VER REPORTES RAP'
```

### **Permisos por Rol**

| Permiso | SUPER ADMIN | ADMIN | INSTRUCTOR |
|---------|-------------|-------|------------|
| VER | ✅ | ✅ | ✅ |
| CREAR | ✅ | ✅ | ✅ |
| EDITAR | ✅ | ✅ | ✅ (solo propios) |
| ELIMINAR | ✅ | ✅ | ❌ |
| GESTIONAR COMPETENCIAS | ✅ | ✅ | ✅ (solo propios) |
| CAMBIAR ESTADO | ✅ | ✅ | ✅ (solo propios) |
| EXPORTAR | ✅ | ✅ | ✅ |
| IMPORTAR | ✅ | ✅ | ❌ |

---

## 🧪 Tests

### **Tests de Feature**

**Archivo:** `tests/Feature/ResultadosAprendizajeCrudTest.php`

Tests implementados:
- ✅ Acceder al listado
- ✅ Crear RAP válido
- ✅ Validar código duplicado
- ✅ Validar duración mínima
- ✅ Validar fechas coherentes
- ✅ Editar RAP
- ✅ Ver detalles
- ✅ Cambiar estado
- ✅ Eliminar RAP sin guías
- ✅ Búsqueda con filtros
- ✅ API de búsqueda

### **Tests Unitarios**

**Archivo:** `tests/Unit/ResultadosAprendizajeModelTest.php`

Tests implementados:
- ✅ Campos fillable
- ✅ Casts de campos
- ✅ Scopes (activos, inactivos, porCodigo)
- ✅ Helpers (isActivo, duracionEnHoras)
- ✅ Relaciones (competencias, users)
- ✅ Atributos (estadoFormateado, nombreCompleto)

### **Ejecutar Tests**

```bash
# Todos los tests
php artisan test

# Tests del módulo específico
php artisan test --filter=ResultadosAprendizaje

# Con cobertura
php artisan test --coverage
```

---

## 🗄️ Estructura de Base de Datos

### **Tabla: resultados_aprendizajes**

```sql
CREATE TABLE resultados_aprendizajes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(500) NOT NULL,
    duracion DECIMAL(8,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    status BOOLEAN DEFAULT 1,
    user_create_id BIGINT UNSIGNED,
    user_edit_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Índices
    INDEX idx_rap_codigo (codigo),
    INDEX idx_rap_status (status),
    INDEX idx_rap_codigo_status (codigo, status),
    INDEX idx_rap_created_at (created_at),
    
    -- Claves foráneas
    FOREIGN KEY (user_create_id) REFERENCES users(id),
    FOREIGN KEY (user_edit_id) REFERENCES users(id)
);
```

### **Tabla: resultados_aprendizaje_competencia**

```sql
CREATE TABLE resultados_aprendizaje_competencia (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    rap_id BIGINT UNSIGNED NOT NULL,
    competencia_id BIGINT UNSIGNED NOT NULL,
    user_create_id BIGINT UNSIGNED,
    user_edit_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Índices
    INDEX idx_rap_comp_rap (rap_id),
    INDEX idx_rap_comp_competencia (competencia_id),
    INDEX idx_rap_comp_both (competencia_id, rap_id),
    
    -- Claves foráneas
    FOREIGN KEY (rap_id) REFERENCES resultados_aprendizajes(id),
    FOREIGN KEY (competencia_id) REFERENCES competencias(id),
    FOREIGN KEY (user_create_id) REFERENCES users(id),
    FOREIGN KEY (user_edit_id) REFERENCES users(id)
);
```

---

## 📖 Ejemplos de Uso

### **Crear RAP con Competencia**

```php
use App\Models\ResultadosAprendizaje;

$rap = ResultadosAprendizaje::create([
    'codigo' => 'RAP001',
    'nombre' => 'Aplicar principios de programación',
    'duracion' => 40,
    'fecha_inicio' => '2025-01-01',
    'fecha_fin' => '2025-06-30',
    'status' => 1,
    'user_create_id' => auth()->id(),
    'user_edit_id' => auth()->id(),
]);

$rap->competencias()->attach($competenciaId, [
    'user_create_id' => auth()->id(),
    'user_edit_id' => auth()->id(),
]);
```

### **Buscar RAPs Activos por Competencia**

```php
$raps = ResultadosAprendizaje::activos()
    ->porCompetencia($competenciaId)
    ->ordenadoPorCodigo()
    ->get();
```

### **Verificar si RAP está Vigente**

```php
if ($rap->estaVigente()) {
    // RAP está entre fecha_inicio y fecha_fin
}
```

---

## 🎨 CSS Personalizado

**Archivo:** `resources/css/resultados-aprendizaje.css`

Clases disponibles:
- `.rap-header` - Header con gradient
- `.badge-rap-activo` - Badge verde para activos
- `.badge-rap-inactivo` - Badge rojo para inactivos
- `.rap-card` - Tarjetas con hover effect
- `.rap-filters` - Contenedor de filtros
- `.btn-rap-primary` - Botón con gradient SENA

---

## 📞 Soporte

Para reportar bugs o solicitar features, contacta al equipo de desarrollo o crea un issue en el repositorio.

---

## 📝 Changelog

### **v1.0.0** - 2025-10-07
- ✅ Implementación completa del módulo RAP
- ✅ CRUD con validaciones de negocio
- ✅ Búsqueda avanzada con 14 filtros
- ✅ Gestión de competencias
- ✅ Sistema de permisos completo
- ✅ Tests unitarios e integración
- ✅ Documentación completa
- ✅ 7 índices de base de datos
- ✅ CSS personalizado
- ✅ API JSON para AJAX

---

**Desarrollado por:** Equipo CDATTG - SENA  
**Tecnologías:** Laravel 11, AdminLTE, Vite, Select2, SweetAlert2
