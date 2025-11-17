# Guía Completa: Sistema de Colas - Laravel Queue Workers

## 📋 Inventario de Jobs del Proyecto

| Job | Timeout | Cola Actual | Cola Optimizada | Propósito |
|-----|---------|-------------|-----------------|-----------|
| `ProcessPersonaImportJob` | 2300s (38min) | `persona-import` | `long-running` | Importación masiva de personas desde Excel |
| `GenerarCarnetsMasivosJob` | 1800s (30min) | `default` | `heavy` | Generación masiva de carnets PDF |
| `EnviarNotificacionMasivaJob` | 600s (10min) | `default` | `heavy` | Envío masivo de notificaciones |
| `ProcesarAsistenciasMasivasJob` | 600s (10min) | `default` | `heavy` | Procesamiento masivo de asistencias |
| `GenerarReporteAsistenciaJob` | 300s (5min) | `default` | `default` | Generación de reportes individuales |
| `ValidarSofiaJob` | 60s (default) | `default` | `default` | Validación en SenaSofiaPlus (lotes de 5) |
| `ValidarDocumentoJob` | 60s (default) | `default` | `default` | Validación de documentos en Google Drive |

## 🎯 Estrategia de Colas Optimizada

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

## ⚙️ Configuración de Colas

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

```bash
# Configuración de Colas
QUEUE_CONNECTION=database

# Timeouts personalizados (opcional)
QUEUE_DEFAULT_RETRY_AFTER=90
QUEUE_HEAVY_RETRY_AFTER=1800
QUEUE_LONG_RUNNING_RETRY_AFTER=2400
```

### 3. Actualizar Jobs para Usar Colas Correctas

#### Jobs que van a `heavy`:

```php
// app/Jobs/GenerarCarnetsMasivosJob.php
public function __construct(Collection $aprendices)
{
    $this->aprendices = $aprendices;
    $this->onQueue('heavy'); // ← Agregar esta línea
}
```

```php
// app/Jobs/EnviarNotificacionMasivaJob.php
public function __construct(Collection $aprendices, string $mensaje, string $tipo = 'general')
{
    $this->aprendices = $aprendices;
    $this->mensaje = $mensaje;
    $this->tipo = $tipo;
    $this->onQueue('heavy'); // ← Agregar esta línea
}
```

```php
// app/Jobs/ProcesarAsistenciasMasivasJob.php
public function __construct(array $asistencias, int $caracterizacionId)
{
    $this->asistencias = $asistencias;
    $this->caracterizacionId = $caracterizacionId;
    $this->onQueue('heavy'); // ← Agregar esta línea
}
```

#### Jobs que van a `long-running`:

```php
// app/Jobs/ProcessPersonaImportJob.php
// Ya está configurado correctamente, solo cambiar el nombre de la cola:
private const QUEUE = 'long-running'; // Cambiar de 'persona-import' a 'long-running'
```

#### Jobs que quedan en `default`:
- `GenerarReporteAsistenciaJob` ✓
- `ValidarSofiaJob` ✓
- `ValidarDocumentoJob` ✓

## 🚀 Despliegue en Producción

### Opción A: Worker Único (Desarrollo/Testing)

```bash
# Procesa TODAS las colas en orden de prioridad
php artisan queue:work --queue=long-running,heavy,default --tries=3 --timeout=2300
```

**Ventajas:**
- Un solo proceso
- Simple de configurar

**Desventajas:**
- Un job largo bloquea los demás
- No se aprovecha el procesamiento paralelo

---

### Opción B: Workers Separados (Producción - RECOMENDADO)

#### Linux con Supervisor

**1. Instalar Supervisor:**

```bash
sudo apt-get update
sudo apt-get install supervisor
```

**2. Crear archivo de configuración:**

```bash
sudo nano /etc/supervisor/conf.d/academica-workers.conf
```

**3. Contenido del archivo:**

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

**4. Activar y reiniciar Supervisor:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

**5. Comandos útiles de Supervisor:**

```bash
# Ver estado de todos los workers
sudo supervisorctl status

# Reiniciar todos los workers
sudo supervisorctl restart all

# Reiniciar un worker específico
sudo supervisorctl restart academica-long-running

# Detener todos los workers
sudo supervisorctl stop all

# Ver logs en tiempo real
sudo tail -f /var/log/supervisor/academica-*.log
```

---

### Opción C: Systemd (Alternativa a Supervisor)

**1. Crear archivo de servicio para cada cola:**

```bash
# Long-running queue
sudo nano /etc/systemd/system/academica-queue-long.service
```

```ini
[Unit]
Description=Laravel Queue Worker - Long Running Jobs
After=network.target mysql.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/academica_web
ExecStart=/usr/bin/php artisan queue:work --queue=long-running --tries=3 --timeout=2300 --sleep=3 --max-jobs=1000
Restart=always
RestartSec=3
StandardOutput=append:/var/log/academica-queue-long.log
StandardError=append:/var/log/academica-queue-long.log

[Install]
WantedBy=multi-user.target
```

```bash
# Heavy queue
sudo nano /etc/systemd/system/academica-queue-heavy.service
```

```ini
[Unit]
Description=Laravel Queue Worker - Heavy Jobs
After=network.target mysql.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/academica_web
ExecStart=/usr/bin/php artisan queue:work --queue=heavy --tries=3 --timeout=1750 --sleep=3 --max-jobs=1000
Restart=always
RestartSec=3
StandardOutput=append:/var/log/academica-queue-heavy.log
StandardError=append:/var/log/academica-queue-heavy.log

[Install]
WantedBy=multi-user.target
```

```bash
# Default queue
sudo nano /etc/systemd/system/academica-queue-default.service
```

```ini
[Unit]
Description=Laravel Queue Worker - Default Jobs
After=network.target mysql.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/academica_web
ExecStart=/usr/bin/php artisan queue:work --queue=default --tries=3 --timeout=85 --sleep=3 --max-jobs=1000
Restart=always
RestartSec=3
StandardOutput=append:/var/log/academica-queue-default.log
StandardError=append:/var/log/academica-queue-default.log

[Install]
WantedBy=multi-user.target
```

**2. Activar servicios:**

```bash
sudo systemctl daemon-reload
sudo systemctl enable academica-queue-long.service
sudo systemctl enable academica-queue-heavy.service
sudo systemctl enable academica-queue-default.service
sudo systemctl start academica-queue-long.service
sudo systemctl start academica-queue-heavy.service
sudo systemctl start academica-queue-default.service
```

**3. Comandos útiles de systemd:**

```bash
# Ver estado
sudo systemctl status academica-queue-*.service

# Reiniciar
sudo systemctl restart academica-queue-long.service

# Ver logs
sudo journalctl -u academica-queue-long.service -f

# Detener
sudo systemctl stop academica-queue-*.service
```

---

### Opción D: Windows (Desarrollo Local)

#### Opción D1: Ejecutar Manualmente

```cmd
REM Terminal 1: Long-running jobs
cd C:\laragon\www\academica_web
php artisan queue:work --queue=long-running --tries=3 --timeout=2300

REM Terminal 2: Heavy jobs
cd C:\laragon\www\academica_web
php artisan queue:work --queue=heavy --tries=3 --timeout=1750

REM Terminal 3: Default jobs
cd C:\laragon\www\academica_web
php artisan queue:work --queue=default --tries=3 --timeout=85
```

#### Opción D2: Tarea Programada de Windows

1. Abrir "Programador de tareas"
2. Crear tarea básica
3. Configurar para cada cola:

**Long-running:**
- Nombre: `Laravel Queue Worker - Long Running`
- Programa: `C:\laragon\bin\php\php-8.2.14\php.exe`
- Argumentos: `artisan queue:work --queue=long-running --tries=3 --timeout=2300`
- Directorio: `C:\laragon\www\academica_web`
- Desencadenador: Al iniciar sesión

**Heavy:**
- Nombre: `Laravel Queue Worker - Heavy`
- Programa: `C:\laragon\bin\php\php-8.2.14\php.exe`
- Argumentos: `artisan queue:work --queue=heavy --tries=3 --timeout=1750`
- Directorio: `C:\laragon\www\academica_web`
- Desencadenador: Al iniciar sesión

**Default:**
- Nombre: `Laravel Queue Worker - Default`
- Programa: `C:\laragon\bin\php\php-8.2.14\php.exe`
- Argumentos: `artisan queue:work --queue=default --tries=3 --timeout=85`
- Directorio: `C:\laragon\www\academica_web`
- Desencadenador: Al iniciar sesión

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

# Reintentar un job específico
php artisan queue:retry <job-id>

# Limpiar jobs fallidos
php artisan queue:flush

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Verificar importaciones pendientes
php artisan tinker --execute="DB::table('persona_imports')->where('status', 'pending')->count();"
```

### Dashboard de Monitoreo (Opcional)

Instalar Laravel Horizon para una UI visual:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
php artisan horizon
```

Acceso: `http://tu-dominio.com/horizon`

---

## 🔧 Solución de Problemas

### Problema: Jobs no se procesan

**Diagnóstico:**
```bash
# Verificar si hay workers corriendo
ps aux | grep "queue:work"

# Verificar jobs en cola
php artisan tinker --execute="DB::table('jobs')->count();"

# Ver logs
tail -50 storage/logs/laravel.log
```

**Solución:**
- Asegúrate de que los workers estén corriendo
- Verifica que estén escuchando la cola correcta
- Revisa los logs para errores

---

### Problema: Importaciones quedan en PENDIENTE

**Diagnóstico:**
```bash
# Ver importaciones pendientes
php artisan tinker --execute="DB::table('persona_imports')->where('status', 'pending')->get(['id', 'created_at', 'original_name']);"

# Ver jobs en cola long-running
php artisan tinker --execute="DB::table('jobs')->where('queue', 'long-running')->count();"
```

**Solución:**

1. **Reiniciar worker:**
   ```bash
   sudo supervisorctl restart academica-long-running
   ```

2. **Procesar manualmente:**
   ```bash
   php artisan queue:work --queue=long-running --once
   ```

---

### Problema: Workers consumiendo mucha memoria

**Solución:**

Ajustar `--max-jobs` para reiniciar workers periódicamente:

```bash
php artisan queue:work --queue=default --max-jobs=100
```

O configurar `--max-time` (en segundos):

```bash
php artisan queue:work --queue=default --max-time=3600
```

---

### Problema: Jobs fallando constantemente

**Diagnóstico:**
```bash
# Ver jobs fallidos
php artisan queue:failed

# Ver detalles de un job fallido
php artisan queue:failed:show <job-id>
```

**Solución:**

1. **Revisar logs:**
   ```bash
   tail -100 storage/logs/laravel.log
   ```

2. **Corregir y reintentar:**
   ```bash
   # Corregir el código
   # Luego reintentar
   php artisan queue:retry <job-id>
   ```

3. **Limpiar si es necesario:**
   ```bash
   php artisan queue:flush
   ```

---

## 📝 Checklist de Despliegue

- [ ] Actualizar `config/queue.php` con las 3 colas
- [ ] Actualizar `.env` con `QUEUE_CONNECTION=database`
- [ ] Modificar los 4 jobs para usar la cola correcta (ver sección 3)
- [ ] Ejecutar `php artisan config:cache`
- [ ] Instalar Supervisor (producción Linux)
- [ ] Crear archivo de configuración de Supervisor
- [ ] Reiniciar Supervisor
- [ ] Verificar que los workers estén corriendo: `sudo supervisorctl status`
- [ ] Probar con una importación pequeña
- [ ] Monitorear logs: `sudo tail -f /var/log/supervisor/academica-*.log`
- [ ] Configurar alertas para jobs fallidos (opcional)
- [ ] Documentar para el equipo

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

## 📞 Soporte

Para más información sobre colas en Laravel:
- [Documentación oficial de Laravel Queues](https://laravel.com/docs/queues)
- [Laravel Horizon para monitoreo](https://laravel.com/docs/horizon)
- [Supervisor](http://supervisord.org/)

---

**Última actualización:** 2025-11-17  
**Versión:** 2.0  
**Proyecto:** Académica Web - Sistema de Colas Optimizado
