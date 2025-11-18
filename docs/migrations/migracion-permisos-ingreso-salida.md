# Migración de Permisos: Talento Humano → Ingreso y Salida

## Descripción

Esta migración actualiza el permiso `VER TALENTO HUMANO` a `VER INGRESO SALIDA` de forma segura sin perder datos existentes.

## Archivo de Migración

`database/migrations/YYYY_MM_DD_HHMMSS_migrate_talento_humano_to_ingreso_salida_permission.php`

## ¿Qué hace la migración?

### En `up()` (aplicar):
1. **Crea el nuevo permiso** `VER INGRESO SALIDA` si no existe
2. **Busca el permiso antiguo** `VER TALENTO HUMANO`
3. **Migra permisos de roles**: Asigna el nuevo permiso a todos los roles que tenían el antiguo
4. **Migra permisos de usuarios**: Asigna el nuevo permiso a todos los usuarios que tenían el antiguo directamente
5. **Registra logs** de todas las operaciones para auditoría
6. **NO elimina el permiso antiguo** (se mantiene por compatibilidad)

### En `down()` (revertir):
1. Restaura el permiso antiguo `VER TALENTO HUMANO` si no existe
2. Asigna el permiso antiguo a todos los roles/usuarios que tienen el nuevo
3. Permite revertir la migración si es necesario

## Ejecución en Producción

### Paso 1: Backup de Base de Datos
```bash
# Crear backup antes de ejecutar la migración
php artisan backup:run
# O manualmente:
mysqldump -u usuario -p nombre_base_datos > backup_antes_migracion_permisos.sql
```

### Paso 2: Ejecutar la Migración
```bash
# En modo mantenimiento (opcional pero recomendado)
php artisan down

# Ejecutar la migración
php artisan migrate

# Verificar que se ejecutó correctamente
php artisan migrate:status

# Salir del modo mantenimiento
php artisan up
```

### Paso 3: Verificar Resultados
```bash
# Verificar que el nuevo permiso existe
php artisan tinker
>>> Spatie\Permission\Models\Permission::where('name', 'VER INGRESO SALIDA')->first();

# Verificar que los roles tienen el nuevo permiso
>>> $rol = Spatie\Permission\Models\Role::where('name', 'ADMINISTRADOR')->first();
>>> $rol->hasPermissionTo('VER INGRESO SALIDA');
```

### Paso 4: Verificar Logs
Revisar `storage/logs/laravel.log` para confirmar que la migración se ejecutó correctamente:
```bash
tail -f storage/logs/laravel.log | grep "Migración de permisos"
```

## Seguridad

✅ **La migración es segura porque:**
- Usa transacciones DB (si algo falla, se revierte todo)
- NO elimina el permiso antiguo (mantiene compatibilidad)
- Asigna el nuevo permiso sin quitar el antiguo
- Registra todas las operaciones en logs
- Es reversible con `php artisan migrate:rollback`

## Post-Migración (Opcional)

Después de verificar que todo funciona correctamente en producción, puedes:

1. **Eliminar el permiso antiguo manualmente** (solo si estás seguro):
```php
// En tinker o un comando artisan
$permisoAntiguo = Permission::where('name', 'VER TALENTO HUMANO')->first();
if ($permisoAntiguo) {
    // Verificar que nadie lo usa
    $roles = $permisoAntiguo->roles;
    $usuarios = DB::table('model_has_permissions')
        ->where('permission_id', $permisoAntiguo->id)
        ->count();
    
    if ($roles->isEmpty() && $usuarios == 0) {
        $permisoAntiguo->delete();
    }
}
```

2. **Actualizar el seeder** para futuras instalaciones (ya hecho en `RolePermissionSeeder.php`)

## Rollback (Si es necesario)

Si necesitas revertir la migración:
```bash
php artisan migrate:rollback --step=1
```

Esto ejecutará el método `down()` y restaurará el permiso antiguo.

## Notas Importantes

- ⚠️ **NO ejecutes** `php artisan db:seed --class=RolePermissionSeeder` en producción después de la migración, ya que podría duplicar permisos
- ✅ El permiso antiguo se mantiene para no romper referencias existentes
- ✅ Los usuarios/roles tendrán AMBOS permisos temporalmente (no causa problemas)
- ✅ La aplicación funcionará correctamente con cualquiera de los dos permisos hasta que se limpie el antiguo

