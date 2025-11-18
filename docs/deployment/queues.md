# ⚙️ Sistema de Colas - Laravel Queue Workers

Guía completa para configurar, desplegar y mantener el sistema de colas en producción.

> **Nota**: Esta guía consolida información de `QUEUE_WORKER_INSTRUCTIONS.md` y `QUEUE_MIGRATION_SUMMARY.md`.

---

## 📋 Inventario de Jobs del Proyecto

| Job | Timeout | Cola | Propósito |
|-----|---------|------|-----------|
| `ProcessPersonaImportJob` | 2300s (38min) | `long-running` | Importación masiva de personas desde Excel |
| `GenerarCarnetsMasivosJob` | 1800s (30min) | `heavy` | Generación masiva de carnets PDF |
| `EnviarNotificacionMasivaJob` | 600s (10min) | `heavy` | Envío masivo de notificaciones |
| `ProcesarAsistenciasMasivasJob` | 600s (10min) | `heavy` | Procesamiento masivo de asistencias |
| `GenerarReporteAsistenciaJob` | 300s (5min) | `default` | Generación de reportes individuales |
| `ValidarSofiaJob` | 60s | `default` | Validación en SenaSofiaPlus (lotes de 5) |
| `ValidarDocumentoJob` | 60s | `default` | Validación de documentos en Google Drive |

---

## 🎯 Estrategia de Colas

### 3 Colas por Tipo de Trabajo

```
┌─────────────────────────────────────────────────────────────────┐
│                    SISTEMA DE COLAS                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────┐     ┌─────────────┐     ┌─────────────┐      │
│  │   DEFAULT   │     │    HEAVY    │     │LONG-RUNNING │      │
│  │             │     │             │     │             │      │
│  │   90s max   │     │  1800s max  │     │  2400s max  │      │
│  │             │     │             │     │             │      │
│  │  - Reportes │     │  - Carnets  │     │  - Imports  │      │
│  │  - Validar  │     │  - Notif.   │     │             │      │
│  │    Sofia    │     │    Masivas  │     │             │      │
│  │  - Validar  │     │  - Asist.   │     │             │      │
│  │    Docs     │     │    Masivas  │     │             │      │
│  └─────────────┘     └─────────────┘     └─────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## ⚙️ Configuración

### 1. Actualizar `config/queue.php`

```php
<?php

return [
    'default' => env('QUEUE_CONNECTION', 'database'),

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],

        // Cola para trabajos pesados (10-30 minutos)
        'heavy' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'heavy',
            'retry_after' => 1800, // 30 minutos
            'after_commit' => false,
        ],

        // Cola para trabajos muy largos (30+ minutos)
        'long-running' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'long-running',
            'retry_after' => 2400, // 40 minutos
            'after_commit' => false,
        ],
    ],

    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],
];
```

### 2. Variables de Entorno `.env`

```env
QUEUE_CONNECTION=database
QUEUE_DEFAULT_RETRY_AFTER=90
QUEUE_HEAVY_RETRY_AFTER=1800
QUEUE_LONG_RUNNING_RETRY_AFTER=2400
```

### 3. Actualizar Jobs

Los jobs deben especificar su cola en el constructor:

```php
// app/Jobs/ProcessPersonaImportJob.php
private const QUEUE = 'long-running';

// app/Jobs/GenerarCarnetsMasivosJob.php
public function __construct(Collection $aprendices)
{
    $this->aprendices = $aprendices;
    $this->onQueue('heavy');
}

// app/Jobs/EnviarNotificacionMasivaJob.php
public function __construct(Collection $aprendices, string $mensaje, string $tipo = 'general')
{
    $this->aprendices = $aprendices;
    $this->mensaje = $mensaje;
    $this->tipo = $tipo;
    $this->onQueue('heavy');
}

// app/Jobs/ProcesarAsistenciasMasivasJob.php
public function __construct(array $asistencias, int $caracterizacionId)
{
    $this->asistencias = $asistencias;
    $this->caracterizacionId = $caracterizacionId;
    $this->onQueue('heavy');
}
```

---

## 🚀 Despliegue

### Desarrollo (Worker Único)

```bash
# Procesa TODAS las colas en orden de prioridad
php artisan queue:work --queue=long-running,heavy,default --tries=3 --timeout=2300
```

### Producción (Workers Separados - RECOMENDADO)

#### Opción A: Supervisor (Linux)

**1. Instalar Supervisor:**
```bash
sudo apt-get update
sudo apt-get install supervisor
```

**2. Crear configuración:**
```bash
sudo nano /etc/supervisor/conf.d/academica-workers.conf
```

**3. Contenido:**
```ini
; Worker para jobs largos (1 proceso)
[program:academica-long-running]
process_name=%(program_name)s
command=php /var/www/academica_web/artisan queue:work --queue=long-running --tries=3 --timeout=2300 --sleep=3 --max-jobs=1000
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/academica-long-running.log
stopwaitsecs=3600

; Workers para jobs pesados (2 procesos)
[program:academica-heavy]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/academica_web/artisan queue:work --queue=heavy --tries=3 --timeout=1750 --sleep=3 --max-jobs=1000
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/academica-heavy.log
stopwaitsecs=1800

; Workers para jobs normales (3 procesos)
[program:academica-default]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/academica_web/artisan queue:work --queue=default --tries=3 --timeout=85 --sleep=3 --max-jobs=1000
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=3
redirect_stderr=true
stdout_logfile=/var/log/supervisor/academica-default.log
stopwaitsecs=90
```

**4. Activar:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

**5. Comandos útiles:**
```bash
sudo supervisorctl status              # Ver estado
sudo supervisorctl restart all         # Reiniciar todos
sudo supervisorctl restart academica-long-running  # Reiniciar uno
sudo tail -f /var/log/supervisor/academica-*.log  # Ver logs
```

#### Opción B: Systemd (Linux)

Ver configuración completa en el [README principal](../README.md#-despliegue-a-producción).

#### Opción C: Windows (Desarrollo)

```cmd
REM Terminal 1: Long-running jobs
php artisan queue:work --queue=long-running --tries=3 --timeout=2300

REM Terminal 2: Heavy jobs
php artisan queue:work --queue=heavy --tries=3 --timeout=1750

REM Terminal 3: Default jobs
php artisan queue:work --queue=default --tries=3 --timeout=85
```

---

## 📊 Monitoreo y Mantenimiento

### Comandos de Diagnóstico

```bash
# Ver estado de las colas
php artisan queue:monitor default,heavy,long-running

# Ver jobs en cada cola
php artisan tinker --execute="DB::table('jobs')->select('queue', DB::raw('count(*) as total'))->groupBy('queue')->get();"

# Ver jobs fallidos
php artisan queue:failed

# Reintentar todos los jobs fallidos
php artisan queue:retry all

# Limpiar jobs fallidos
php artisan queue:flush

# Ver logs
tail -f storage/logs/laravel.log
```

### Laravel Horizon (Dashboard Visual)

El proyecto incluye Laravel Horizon para monitoreo visual:

```bash
# Acceder al dashboard
# URL: https://tudominio.com/horizon

# Comandos
php artisan horizon              # Iniciar Horizon
php artisan horizon:status      # Ver estado
php artisan horizon:terminate  # Reiniciar
```

Ver [Laravel Horizon](horizon.md) para más detalles.

---

## 🔧 Solución de Problemas

### Problema: Jobs no se procesan

**Diagnóstico:**
```bash
ps aux | grep "queue:work"
php artisan tinker --execute="DB::table('jobs')->count();"
tail -50 storage/logs/laravel.log
```

**Solución:**
- Verificar que los workers estén corriendo
- Verificar que estén escuchando la cola correcta
- Revisar logs para errores

### Problema: Importaciones quedan en PENDIENTE

**Diagnóstico:**
```bash
php artisan tinker --execute="DB::table('persona_imports')->where('status', 'pending')->get(['id', 'created_at']);"
php artisan tinker --execute="DB::table('jobs')->where('queue', 'long-running')->count();"
```

**Solución:**
```bash
sudo supervisorctl restart academica-long-running
php artisan queue:work --queue=long-running --once
```

### Problema: Workers consumiendo mucha memoria

**Solución:**
```bash
# Ajustar max-jobs para reiniciar periódicamente
php artisan queue:work --queue=default --max-jobs=100

# O configurar max-time
php artisan queue:work --queue=default --max-time=3600
```

---

## 🎓 Conceptos Clave

### ¿Por qué múltiples colas?

1. **Prioridad**: Jobs urgentes no se bloquean por jobs largos
2. **Recursos**: Asignar más workers a colas críticas
3. **Timeouts**: Configuraciones diferentes por tipo de trabajo
4. **Monitoreo**: Estadísticas separadas por tipo

### ¿Por qué timeout < retry_after?

```
Job Timeout:    2300s ━━━━━━━━━━━━━━┓
                                    ┃ Job falla
Retry After:    2400s ━━━━━━━━━━━━━━━┻━━┓
                                         ┃ Sistema reintenta
                                         ┗━━ Evita duplicados
```

Si `timeout >= retry_after`, el sistema puede reintentar mientras el job aún está corriendo, creando duplicados.

### ¿Cuántos workers necesito?

**Desarrollo:**
- 1 worker único escuchando todas las colas

**Producción pequeña:**
- 1 worker long-running
- 1 worker heavy
- 2 workers default

**Producción grande:**
- 2 workers long-running
- 3 workers heavy
- 5 workers default

---

## 📝 Checklist de Despliegue

- [ ] Actualizar `config/queue.php` con las 3 colas
- [ ] Actualizar `.env` con `QUEUE_CONNECTION=database`
- [ ] Modificar jobs para usar la cola correcta
- [ ] Ejecutar `php artisan config:cache`
- [ ] Instalar Supervisor (producción Linux)
- [ ] Crear archivo de configuración de Supervisor
- [ ] Reiniciar Supervisor
- [ ] Verificar workers: `sudo supervisorctl status`
- [ ] Probar con una importación pequeña
- [ ] Monitorear logs
- [ ] Configurar alertas para jobs fallidos (opcional)

---

## 📞 Referencias

- [Documentación oficial de Laravel Queues](https://laravel.com/docs/queues)
- [Laravel Horizon](https://laravel.com/docs/horizon)
- [Supervisor](http://supervisord.org/)
- [README Principal](../README.md#-despliegue-a-producción)

---

**Última actualización:** 2025-11-17  
**Versión:** 2.0

