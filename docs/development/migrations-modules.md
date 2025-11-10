# 🎯 Guía Rápida: Migraciones por Módulos

## 📖 Resumen Ejecutivo

El proyecto ha sido reorganizado con **75 migraciones** distribuidas en **15 módulos funcionales** que se pueden ejecutar de forma independiente o en conjunto, respetando las dependencias entre ellos.

## ⚡ Comandos Rápidos

### Ver todos los módulos disponibles
```bash
php artisan migrate:module --list
```

### Migrar todo el sistema desde cero
```bash
php artisan migrate:module --all --fresh
```

### Migrar un módulo específico
```bash
php artisan migrate:module batch_01_sistema_base
```

### Migrar todos los módulos (sin limpiar BD)
```bash
php artisan migrate:module --all
```

## 📂 Módulos Disponibles

| Orden | Módulo | Descripción | Tablas |
|-------|--------|-------------|--------|
| 1 | `batch_01_sistema_base` | Sistema base de Laravel | 6 tablas |
| 2 | `batch_02_permisos` | Permisos y roles | 5 tablas |
| 3 | `batch_03_ubicaciones` | Geografía y sedes | 6 tablas |
| 4 | `batch_04_personas` | Datos personales | 1 tabla + modificaciones |
| 5 | `batch_05_infraestructura` | Bloques, pisos, ambientes | 3 tablas |
| 6 | `batch_06_programas` | Programas de formación | 2 tablas |
| 7 | `batch_07_instructores_aprendices` | Instructores y aprendices | 3 tablas |
| 8 | `batch_08_fichas` | Fichas de caracterización | 1 tabla + modificaciones |
| 9 | `batch_09_relaciones` | Tablas pivot | 4 tablas |
| 10 | `batch_10_jornadas_horarios` | Horarios y jornadas | 3 tablas |
| 11 | `batch_11_asistencias` | Control de asistencias | 2 tablas |
| 12 | `batch_12_competencias` | Competencias y guías | 4 tablas |
| 13 | `batch_13_evidencias` | Evidencias | 1 tabla + modificaciones |
| 14 | `batch_14_logs_auditoria` | Logs del sistema | 1 tabla + modificaciones |
| 15 | `batch_15_parametros` | Configuración | 2 tablas |
| 16 | `batch_15_inventario` | Inventario | 6 tablas |

## 🔄 Flujo de Trabajo Recomendado

### Para Desarrollo Local
```bash
# 1. Resetear base de datos y migrar todo
php artisan migrate:module --all --fresh

# 2. Ejecutar seeders
php artisan db:seed
```

### Para Producción
```bash
# 1. Migrar solo los módulos nuevos o modificados
php artisan migrate:module batch_XX_nombre_modulo

# 2. O ejecutar todas las migraciones pendientes
php artisan migrate:module --all
```

### Para Testing
```bash
# En tus tests, usa:
php artisan migrate:fresh --env=testing
# o
php artisan migrate:module --all --fresh --env=testing
```

## 🏗️ Agregar Nueva Migración

### Paso 1: Identificar el módulo correcto
Determina a qué módulo funcional pertenece tu migración según la tabla que modifica.

### Paso 2: Crear el archivo en el batch correcto
```bash
# Ejemplo: Agregar campo a tabla de instructores
touch database/migrations/batch_07_instructores_aprendices/2025_10_27_000066_add_new_field_to_instructors.php
```

### Paso 3: Usar el siguiente timestamp secuencial
- Revisa el último número usado en ese batch
- Incrementa en 1
- Usa el formato: `2025_10_27_NNNNNN_descripcion.php`

### Ejemplo Completo
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->string('nuevo_campo')->nullable()->after('campo_existente');
        });
    }

    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn('nuevo_campo');
        });
    }
};
```

## 🔍 Verificación

### Ver estado de migraciones
```bash
php artisan migrate:status
```

### Ver solo las migraciones de un módulo
```bash
php artisan migrate:status --path=database/migrations/batch_01_sistema_base
```

## ⚠️ Troubleshooting

### Problema: "Base table or view already exists"
**Solución:** Ya tienes migraciones ejecutadas. Usa:
```bash
php artisan migrate:fresh
# o
php artisan migrate:module --all --fresh
```

### Problema: "Class 'X' not found"
**Solución:** El archivo está corrupto o mal nombrado. Verifica:
- Que el nombre del archivo coincida con la clase
- Que use el formato correcto `return new class extends Migration`

### Problema: "Foreign key constraint fails"
**Solución:** Estás intentando migrar un módulo sin migrar sus dependencias. Revisa `database/migrations_batches.php` para ver las dependencias y migra en orden.

### Problema: No se reconoce el comando `migrate:module`
**Solución:** El comando no está registrado. Verifica que existe:
```bash
ls -la app/Console/Commands/MigrateModule.php
```

Si no existe, créalo copiándolo del repositorio o ejecuta:
```bash
php artisan make:command MigrateModule
```

## 📊 Dependencias Entre Módulos

```
batch_01_sistema_base (BASE)
├── batch_02_permisos
├── batch_03_ubicaciones
│   ├── batch_04_personas
│   ├── batch_05_infraestructura
│   └── batch_07_instructores_aprendices
├── batch_06_programas
│   ├── batch_08_fichas
│   │   ├── batch_09_relaciones
│   │   │   └── batch_10_jornadas_horarios
│   │   │       └── batch_11_asistencias
│   │   └── batch_14_logs_auditoria
│   └── batch_12_competencias
│       └── batch_13_evidencias
└── batch_15_parametros
```

## 📚 Recursos Adicionales

- **Documentación completa:** `database/migrations/README.md`
- **Índice de batches:** `database/migrations_batches.php`
- **Código del comando:** `app/Console/Commands/MigrateModule.php`
- **Script de reorganización:** `reorganize_migrations.php`

## 🎓 Mejores Prácticas

1. ✅ **Siempre** ejecuta `migrate:fresh` en desarrollo antes de hacer push
2. ✅ **Nunca** modifiques migraciones que ya están en producción
3. ✅ **Siempre** crea nuevas migraciones para cambios en producción
4. ✅ **Documenta** cambios complejos en los comentarios de la migración
5. ✅ **Prueba** el `up()` y el `down()` antes de commitear
6. ✅ **Respeta** el orden de los batches y sus dependencias

## 💡 Tips

- Usa `--pretend` para ver qué SQL se ejecutaría sin ejecutarlo:
  ```bash
  php artisan migrate --pretend --path=database/migrations/batch_01_sistema_base
  ```

- Para ver el SQL de una migración específica, abre el archivo y revisa los métodos

- Si necesitas rollback, hazlo por módulo en orden inverso:
  ```bash
  php artisan migrate:rollback --path=database/migrations/batch_15_parametros
  php artisan migrate:rollback --path=database/migrations/batch_14_logs_auditoria
  # ... etc
  ```

---

**¿Necesitas ayuda?** Revisa la documentación completa en `database/migrations/README.md` o consulta `migrations_batches.php` para entender las dependencias.

