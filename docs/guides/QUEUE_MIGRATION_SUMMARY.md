# Resumen de Migración - Sistema de Colas

## 📊 Cambios Realizados

### Antes (Configuración antigua)

```
┌────────────────────────────────────────────────┐
│          Cola: persona-import (2400s)          │
│  - ProcessPersonaImportJob                     │
└────────────────────────────────────────────────┘

┌────────────────────────────────────────────────┐
│            Cola: default (90s)                 │
│  - GenerarCarnetsMasivosJob (1800s timeout)    │ ← PROBLEMA
│  - EnviarNotificacionMasivaJob (600s timeout)  │ ← PROBLEMA
│  - ProcesarAsistenciasMasivasJob (600s)        │
│  - GenerarReporteAsistenciaJob (300s)          │
│  - ValidarSofiaJob (60s)                       │
│  - ValidarDocumentoJob (60s)                   │
└────────────────────────────────────────────────┘
```

**Problemas:**
- Jobs con timeout largo (1800s) en cola con retry_after corto (90s)
- Riesgo de duplicación de jobs
- No hay priorización por tipo de trabajo

---

### Después (Configuración optimizada)

```
┌────────────────────────────────────────────────┐
│        Cola: long-running (2400s)              │
│  ✓ ProcessPersonaImportJob (2300s)             │
└────────────────────────────────────────────────┘

┌────────────────────────────────────────────────┐
│            Cola: heavy (1800s)                 │
│  ✓ GenerarCarnetsMasivosJob (1800s)            │
│  ✓ EnviarNotificacionMasivaJob (600s)          │
│  ✓ ProcesarAsistenciasMasivasJob (600s)        │
└────────────────────────────────────────────────┘

┌────────────────────────────────────────────────┐
│            Cola: default (90s)                 │
│  ✓ GenerarReporteAsistenciaJob (300s)          │
│  ✓ ValidarSofiaJob (60s)                       │
│  ✓ ValidarDocumentoJob (60s)                   │
└────────────────────────────────────────────────┘
```

**Mejoras:**
- Cada job en la cola correcta según su timeout
- Sin riesgo de duplicación
- Priorización adecuada
- Escalabilidad

---

## 🔄 Archivos Modificados

### 1. `config/queue.php`
```diff
- 'persona-import' => [
+ 'long-running' => [
      'driver' => 'database',
      'table' => 'jobs',
-     'queue' => 'persona-import',
+     'queue' => 'long-running',
      'retry_after' => 2400,
  ],
  
+ 'heavy' => [
+     'driver' => 'database',
+     'table' => 'jobs',
+     'queue' => 'heavy',
+     'retry_after' => 1800,
+ ],
```

### 2. `app/Jobs/ProcessPersonaImportJob.php`
```diff
- private const QUEUE = 'persona-import';
+ private const QUEUE = 'long-running';
```

### 3. `app/Livewire/PersonaImportComponent.php`
```diff
- ->where('queue', 'persona-import')
+ ->where('queue', 'long-running')
```

### 4. Nuevos archivos a modificar (PENDIENTE):

```php
// app/Jobs/GenerarCarnetsMasivosJob.php
public function __construct(Collection $aprendices)
{
    $this->aprendices = $aprendices;
+   $this->onQueue('heavy'); // ← AGREGAR
}

// app/Jobs/EnviarNotificacionMasivaJob.php
public function __construct(Collection $aprendices, string $mensaje, string $tipo = 'general')
{
    $this->aprendices = $aprendices;
    $this->mensaje = $mensaje;
    $this->tipo = $tipo;
+   $this->onQueue('heavy'); // ← AGREGAR
}

// app/Jobs/ProcesarAsistenciasMasivasJob.php
public function __construct(array $asistencias, int $caracterizacionId)
{
    $this->asistencias = $asistencias;
    $this->caracterizacionId = $caracterizacionId;
+   $this->onQueue('heavy'); // ← AGREGAR
}
```

---

## 🚀 Comandos para Activar

### Desarrollo (Worker único)

**ANTES:**
```bash
php artisan queue:work --queue=persona-import --tries=3 --timeout=2300
```

**AHORA:**
```bash
# Todas las colas en orden de prioridad
php artisan queue:work --queue=long-running,heavy,default --tries=3 --timeout=2300
```

---

### Producción (Workers separados con Supervisor)

**ANTES:**
```bash
php artisan queue:work --queue=persona-import
```

**AHORA:**
```bash
# Worker 1: Long-running (1 proceso)
php artisan queue:work --queue=long-running --tries=3 --timeout=2300

# Worker 2: Heavy (2 procesos)
php artisan queue:work --queue=heavy --tries=3 --timeout=1750

# Worker 3: Default (3 procesos)
php artisan queue:work --queue=default --tries=3 --timeout=85
```

---

## ✅ Checklist de Migración

- [x] Actualizar `config/queue.php`
- [x] Actualizar `ProcessPersonaImportJob.php`
- [x] Actualizar `PersonaImportComponent.php`
- [x] Crear documentación completa (`QUEUE_WORKER_INSTRUCTIONS.md`)
- [ ] Agregar `$this->onQueue('heavy')` a `GenerarCarnetsMasivosJob.php`
- [ ] Agregar `$this->onQueue('heavy')` a `EnviarNotificacionMasivaJob.php`
- [ ] Agregar `$this->onQueue('heavy')` a `ProcesarAsistenciasMasivasJob.php`
- [ ] Reiniciar workers en producción
- [ ] Monitorear logs para verificar funcionamiento
- [ ] Actualizar documentación del equipo

---

## 🎯 Próximos Pasos

1. **Ahora mismo:**
   ```bash
   # Detener worker actual
   Ctrl+C
   
   # Iniciar con nueva configuración
   php artisan queue:work --queue=long-running,heavy,default --tries=3 --timeout=2300
   ```

2. **Para producción:**
   - Seguir guía completa en `QUEUE_WORKER_INSTRUCTIONS.md`
   - Configurar Supervisor con 3 workers separados
   - Monitorear con `supervisorctl status`

3. **Opcional:**
   - Instalar Laravel Horizon para UI visual
   - Configurar alertas para jobs fallidos
   - Implementar logs centralizados

---

## 📞 Soporte

Consultar `QUEUE_WORKER_INSTRUCTIONS.md` para:
- Configuración completa de Supervisor
- Comandos de diagnóstico
- Solución de problemas comunes
- Conceptos clave del sistema de colas

---

**Fecha de migración:** 2025-11-17  
**Estado:** ✅ Completado (pendiente modificar 3 jobs para usar cola `heavy`)

