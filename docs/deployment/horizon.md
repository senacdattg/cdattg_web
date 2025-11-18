# 📊 Laravel Horizon - Dashboard de Colas

Laravel Horizon proporciona un dashboard visual para monitorear las colas de Laravel.

---

## 🚀 Instalación

Horizon ya está incluido en el proyecto. Solo necesitas configurarlo:

```bash
# Publicar configuración (si no está publicada)
php artisan horizon:install

# Migrar tablas de Horizon
php artisan migrate
```

---

## ⚙️ Configuración

### Variables de Entorno `.env`

```env
HORIZON_PREFIX=academica
HORIZON_BALANCE=auto
HORIZON_MAX_PROCESSES=10
HORIZON_MIN_PROCESSES=1
```

### Configuración en `config/horizon.php`

El archivo ya está configurado con las 3 colas del proyecto:
- `default` - Jobs normales
- `heavy` - Jobs pesados
- `long-running` - Jobs largos

---

## 🎯 Uso

### Iniciar Horizon

```bash
# Desarrollo
php artisan horizon

# Producción (con Supervisor)
# Ver configuración en queues.md
```

### Comandos Útiles

```bash
# Ver estado
php artisan horizon:status

# Pausar procesamiento
php artisan horizon:pause

# Reanudar procesamiento
php artisan horizon:continue

# Terminar y reiniciar
php artisan horizon:terminate

# Limpiar métricas
php artisan horizon:clear
```

---

## 📊 Dashboard

### Acceso

```
https://tudominio.com/horizon
```

### Características

- **Métricas en tiempo real**: Jobs procesados, fallidos, pendientes
- **Estadísticas por cola**: Separado por `default`, `heavy`, `long-running`
- **Historial de jobs**: Ver jobs completados y fallidos
- **Reintentos**: Reintentar jobs fallidos desde el dashboard
- **Filtros**: Filtrar por cola, estado, fecha

---

## 🔐 Autenticación

Horizon requiere autenticación. La configuración está en `app/Providers/HorizonServiceProvider.php`:

```php
protected function gate()
{
    Gate::define('viewHorizon', function ($user) {
        return in_array($user->email, [
            'admin@example.com',
        ]);
    });
}
```

Ajusta los emails autorizados según tus necesidades.

---

## 🚀 Producción con Supervisor

Ver configuración completa en [Sistema de Colas](queues.md#opción-a-supervisor-linux).

---

## 📞 Referencias

- [Documentación oficial de Laravel Horizon](https://laravel.com/docs/horizon)
- [Sistema de Colas](queues.md)

---

**Última actualización:** 2025-11-17

