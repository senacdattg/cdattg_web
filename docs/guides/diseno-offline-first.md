# Diseño: Sistema Offline-First para Académica Web

## 1. Arquitectura General

### 1.1. Principios de Diseño

- **Offline-First**: Todas las operaciones se guardan localmente primero
- **Sincronización Asíncrona**: La sincronización ocurre en background
- **Resiliencia**: El sistema funciona completamente sin internet
- **Transparencia**: El usuario no percibe diferencias entre modo online/offline
- **Consistencia Eventual**: Los datos se sincronizan cuando hay conectividad

### 1.2. Componentes Principales

```
┌─────────────────────────────────────────────────────────────┐
│                    CAPA DE APLICACIÓN                        │
│  (Controllers, Livewire Components, Services)                │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              CAPA DE SINCRONIZACIÓN                          │
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │ OfflineService   │  │ NetworkChecker   │                │
│  │ (Orquestador)   │  │ (Conectividad)   │                │
│  └──────────────────┘  └──────────────────┘                │
└───────────────────────┬─────────────────────────────────────┘
                        │
        ┌───────────────┴───────────────┐
        ▼                               ▼
┌──────────────────┐          ┌──────────────────┐
│  CACHE LOCAL     │          │  BD PRINCIPAL    │
│  (SQLite/File)   │          │  (MySQL)         │
│                  │          │                  │
│  sync_queue      │◄────────►│  Tablas          │
│  (Cola pendiente)│          │  principales     │
└──────────────────┘          └──────────────────┘
        │
        ▼
┌──────────────────┐
│  SYNC WORKER     │
│  (Job Queue)     │
│  (Sincronización)│
└──────────────────┘
```

## 2. Estructura de Base de Datos

### 2.1. Tabla `sync_queue` (Cola de Sincronización)

```sql
CREATE TABLE sync_queue (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    operation_type VARCHAR(50) NOT NULL,        -- 'entrada', 'salida', 'import', etc.
    model_class VARCHAR(255) NOT NULL,          -- 'App\Models\PersonaIngresoSalida'
    operation VARCHAR(20) NOT NULL,             -- 'create', 'update', 'delete'
    payload JSON NOT NULL,                     -- Datos completos de la operación
    local_id VARCHAR(100) NULL,                 -- ID temporal local (UUID)
    remote_id BIGINT UNSIGNED NULL,             -- ID en BD principal (después de sync)
    status ENUM('pending', 'syncing', 'synced', 'failed') DEFAULT 'pending',
    retry_count INT DEFAULT 0,
    max_retries INT DEFAULT 5,
    error_message TEXT NULL,
    metadata JSON NULL,                         -- Info adicional (user_id, timestamp, etc.)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    synced_at TIMESTAMP NULL,
    
    INDEX idx_status (status),
    INDEX idx_operation_type (operation_type),
    INDEX idx_created_at (created_at),
    INDEX idx_retry_count (retry_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2. Tabla `sync_conflicts` (Resolución de Conflictos)

```sql
CREATE TABLE sync_conflicts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    sync_queue_id BIGINT UNSIGNED NOT NULL,
    conflict_type VARCHAR(50) NOT NULL,        -- 'duplicate', 'version_mismatch', etc.
    local_data JSON NOT NULL,
    remote_data JSON NOT NULL,
    resolution VARCHAR(50) NULL,                -- 'local_wins', 'remote_wins', 'merge'
    resolved_by BIGINT UNSIGNED NULL,           -- user_id
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sync_queue_id) REFERENCES sync_queue(id) ON DELETE CASCADE,
    INDEX idx_sync_queue_id (sync_queue_id),
    INDEX idx_resolution (resolution)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.3. Tabla `network_status` (Estado de Conectividad)

```sql
CREATE TABLE network_status (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    is_online BOOLEAN DEFAULT FALSE,
    last_check_at TIMESTAMP NULL,
    last_successful_sync TIMESTAMP NULL,
    consecutive_failures INT DEFAULT 0,
    ping_url VARCHAR(255) NULL,
    response_time_ms INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_online (is_online)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 3. Modelos Eloquent

### 3.1. Modelo `SyncQueue`

```php
namespace App\Models;

class SyncQueue extends Model
{
    protected $table = 'sync_queue';
    
    protected $fillable = [
        'operation_type',
        'model_class',
        'operation',
        'payload',
        'local_id',
        'remote_id',
        'status',
        'retry_count',
        'max_retries',
        'error_message',
        'metadata',
    ];
    
    protected $casts = [
        'payload' => 'array',
        'metadata' => 'array',
        'synced_at' => 'datetime',
    ];
    
    // Scopes
    public function scopePending($query) {
        return $query->where('status', 'pending');
    }
    
    public function scopeSyncing($query) {
        return $query->where('status', 'syncing');
    }
    
    public function scopeFailed($query) {
        return $query->where('status', 'failed');
    }
    
    public function scopeNeedsRetry($query) {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', DB::raw('max_retries'));
    }
}
```

### 3.2. Modelo `NetworkStatus`

```php
namespace App\Models;

class NetworkStatus extends Model
{
    protected $table = 'network_status';
    
    protected $fillable = [
        'is_online',
        'last_check_at',
        'last_successful_sync',
        'consecutive_failures',
        'ping_url',
        'response_time_ms',
    ];
    
    protected $casts = [
        'is_online' => 'boolean',
        'last_check_at' => 'datetime',
        'last_successful_sync' => 'datetime',
    ];
    
    // Singleton pattern para el estado de red
    public static function getCurrent(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
```

## 4. Servicios

### 4.1. `OfflineService` (Orquestador Principal)

**Responsabilidades:**
- Interceptar operaciones que requieren sincronización
- Guardar en cola local
- Intentar sincronización inmediata si hay internet
- Retornar respuesta inmediata al usuario

**Métodos principales:**
```php
class OfflineService
{
    /**
     * Guarda una operación en la cola offline
     */
    public function queueOperation(
        string $operationType,
        string $modelClass,
        string $operation,
        array $data,
        ?string $localId = null
    ): SyncQueue;
    
    /**
     * Intenta sincronizar una operación específica
     */
    public function syncOperation(SyncQueue $queueItem): bool;
    
    /**
     * Sincroniza todas las operaciones pendientes
     */
    public function syncAllPending(): array;
    
    /**
     * Procesa una operación desde la cola
     */
    private function processOperation(SyncQueue $queueItem): bool;
}
```

### 4.2. `NetworkChecker` (Verificación de Conectividad)

**Responsabilidades:**
- Verificar si hay conexión a internet
- Hacer ping a endpoints configurados
- Mantener estado de conectividad
- Detectar cambios en conectividad

**Métodos principales:**
```php
class NetworkChecker
{
    /**
     * Verifica si hay conexión a internet
     */
    public function isOnline(): bool;
    
    /**
     * Verifica conectividad con timeout configurable
     */
    public function checkConnectivity(?string $url = null, int $timeout = 5): bool;
    
    /**
     * Obtiene el estado actual de la red
     */
    public function getStatus(): NetworkStatus;
    
    /**
     * Actualiza el estado de la red
     */
    public function updateStatus(bool $isOnline, ?int $responseTime = null): void;
}
```

### 4.3. `SyncWorker` (Job de Sincronización)

**Responsabilidades:**
- Procesar cola de sincronización periódicamente
- Reintentar operaciones fallidas
- Manejar conflictos
- Notificar sobre estado de sincronización

**Implementación:**
```php
class SyncOfflineOperationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle(
        OfflineService $offlineService,
        NetworkChecker $networkChecker
    ): void;
}
```

## 5. Flujo de Operaciones

### 5.1. Flujo de Registro de Entrada (Ejemplo)

```
Usuario hace clic en "Registrar Entrada"
    ↓
Controller/Component llama a PersonaIngresoSalidaService
    ↓
PersonaIngresoSalidaService llama a OfflineService::queueOperation()
    ↓
OfflineService:
    1. Genera UUID local (local_id)
    2. Guarda en sync_queue con status='pending'
    3. Retorna respuesta inmediata al usuario
    ↓
OfflineService intenta sincronización inmediata:
    ├─ NetworkChecker::isOnline()?
    │   ├─ SÍ → Intenta syncOperation()
    │   │   ├─ Éxito → status='synced', elimina de cola
    │   │   └─ Falla → status='failed', queda en cola
    │   └─ NO → Deja en cola, status='pending'
    ↓
Usuario recibe respuesta inmediata (éxito)
    ↓
[Background] SyncWorker procesa cola cada X minutos
```

### 5.2. Flujo de Sincronización en Background

```
SyncWorker se ejecuta (cada 5 minutos)
    ↓
NetworkChecker::isOnline()?
    ├─ NO → Termina, no hace nada
    └─ SÍ → Continúa
    ↓
OfflineService::syncAllPending()
    ↓
Para cada item en sync_queue (status='pending' o 'failed'):
    1. Cambia status a 'syncing'
    2. Intenta ejecutar operación en BD principal
    3. Si éxito:
       - status='synced'
       - Guarda remote_id
       - Elimina de cola (o marca como completado)
    4. Si falla:
       - status='failed'
       - Incrementa retry_count
       - Guarda error_message
       - Si retry_count >= max_retries → Notifica administrador
    ↓
Actualiza NetworkStatus::last_successful_sync
```

## 6. Integración con Servicios Existentes

### 6.1. Modificación de `PersonaIngresoSalidaService`

**Antes:**
```php
public function registrarEntrada(...): PersonaIngresoSalida
{
    return DB::transaction(function () use (...) {
        // Validaciones
        $registro = PersonaIngresoSalida::create([...]);
        return $registro;
    });
}
```

**Después (con Offline-First):**
```php
public function registrarEntrada(...): PersonaIngresoSalida
{
    // Generar ID local temporal
    $localId = Str::uuid()->toString();
    
    // Guardar en cola offline
    $syncQueue = app(OfflineService::class)->queueOperation(
        'persona_ingreso_salida',
        PersonaIngresoSalida::class,
        'create',
        [
            'persona_id' => $personaId,
            'sede_id' => $sedeId,
            // ... resto de datos
        ],
        $localId
    );
    
    // Crear modelo temporal para respuesta inmediata
    $registro = new PersonaIngresoSalida([
        'id' => $localId, // ID temporal
        'persona_id' => $personaId,
        // ... resto de datos
    ]);
    $registro->setAttribute('is_pending_sync', true);
    
    // Intentar sincronización inmediata (no bloquea)
    dispatch(new SyncOfflineOperationsJob())->afterResponse();
    
    return $registro;
}
```

### 6.2. Middleware Opcional para Auto-Sincronización

```php
class AutoSyncMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // Si hay operaciones pendientes y hay internet, intentar sync
        if (app(NetworkChecker::class)->isOnline()) {
            $pendingCount = SyncQueue::pending()->count();
            if ($pendingCount > 0) {
                dispatch(new SyncOfflineOperationsJob())->afterResponse();
            }
        }
        
        return $response;
    }
}
```

## 7. Manejo de Conflictos

### 7.1. Tipos de Conflictos

1. **Duplicado**: Mismo registro creado offline y online
2. **Versión desactualizada**: Registro modificado offline mientras estaba actualizado online
3. **Dependencia faltante**: Registro offline depende de otro que no existe

### 7.2. Estrategias de Resolución

```php
class ConflictResolver
{
    /**
     * Resuelve conflictos automáticamente según reglas de negocio
     */
    public function resolve(SyncQueue $queueItem, \Exception $exception): ?string
    {
        // Estrategia: Última escritura gana (timestamp)
        if ($this->isTimestampConflict($exception)) {
            return $this->resolveLastWriteWins($queueItem);
        }
        
        // Estrategia: Merge inteligente
        if ($this->isMergeableConflict($exception)) {
            return $this->resolveMerge($queueItem);
        }
        
        // Estrategia: Requiere intervención manual
        return null; // Se guarda en sync_conflicts para revisión
    }
}
```

## 8. Interfaz de Usuario

### 8.1. Indicadores de Estado

- **Badge de sincronización**: Muestra cantidad de operaciones pendientes
- **Notificación toast**: Cuando se completa sincronización
- **Banner de estado**: Si está en modo offline
- **Lista de pendientes**: Vista de operaciones en cola

### 8.2. Componente Livewire para Estado de Sincronización

```php
class SyncStatusComponent extends Component
{
    public $pendingCount = 0;
    public $isOnline = true;
    public $lastSync = null;
    
    public function mount()
    {
        $this->updateStatus();
    }
    
    public function updateStatus()
    {
        $this->pendingCount = SyncQueue::pending()->count();
        $this->isOnline = app(NetworkChecker::class)->isOnline();
        $this->lastSync = NetworkStatus::getCurrent()->last_successful_sync;
    }
    
    public function render()
    {
        return view('livewire.sync-status-component');
    }
}
```

## 9. Configuración

### 9.1. Archivo de Configuración `config/offline.php`

```php
return [
    'enabled' => env('OFFLINE_MODE_ENABLED', true),
    
    'sync' => [
        'interval' => env('SYNC_INTERVAL_MINUTES', 5),
        'max_retries' => env('SYNC_MAX_RETRIES', 5),
        'retry_delay' => env('SYNC_RETRY_DELAY_SECONDS', 60),
    ],
    
    'network' => [
        'check_url' => env('NETWORK_CHECK_URL', 'https://www.google.com'),
        'timeout' => env('NETWORK_CHECK_TIMEOUT', 5),
        'check_interval' => env('NETWORK_CHECK_INTERVAL_SECONDS', 30),
    ],
    
    'operations' => [
        'persona_ingreso_salida' => [
            'enabled' => true,
            'priority' => 'high',
        ],
        'persona_import' => [
            'enabled' => true,
            'priority' => 'medium',
        ],
        // ... más operaciones
    ],
];
```

## 10. Casos de Uso Específicos

### 10.1. Registro de Entrada/Salida

**Escenario**: Usuario registra entrada sin internet
1. Se guarda en `sync_queue` con todos los datos
2. Usuario ve confirmación inmediata
3. Cuando hay internet, se sincroniza automáticamente
4. Si hay conflicto (ej: entrada ya registrada), se maneja según reglas

### 10.2. Importación Masiva

**Escenario**: Usuario inicia importación sin internet
1. Se guarda el archivo localmente
2. Se crea registro en `sync_queue` con referencia al archivo
3. Cuando hay internet, se procesa la importación
4. Se notifica al usuario cuando termine

### 10.3. Múltiples Dispositivos

**Consideración**: Si el mismo usuario usa múltiples dispositivos offline
- Cada dispositivo tiene su propia cola local
- Al sincronizar, puede haber conflictos
- Resolución: Timestamp más reciente gana, o merge según reglas

## 11. Consideraciones de Seguridad

1. **Validación de datos**: Validar antes de guardar en cola
2. **Sanitización**: Limpiar datos antes de sincronizar
3. **Autenticación**: Mantener user_id en metadata para auditoría
4. **Rate limiting**: Limitar cantidad de operaciones pendientes por usuario
5. **Tamaño de payload**: Limitar tamaño de datos en sync_queue

## 12. Monitoreo y Logging

### 12.1. Métricas a Monitorear

- Cantidad de operaciones pendientes
- Tasa de éxito de sincronización
- Tiempo promedio de sincronización
- Cantidad de conflictos
- Tiempo de respuesta de verificación de red

### 12.2. Logs Importantes

```php
Log::info('Operación encolada offline', [
    'operation_type' => $operationType,
    'local_id' => $localId,
]);

Log::info('Sincronización exitosa', [
    'sync_queue_id' => $queueItem->id,
    'remote_id' => $remoteId,
]);

Log::warning('Sincronización fallida', [
    'sync_queue_id' => $queueItem->id,
    'error' => $exception->getMessage(),
    'retry_count' => $queueItem->retry_count,
]);
```

## 13. Plan de Implementación

### Fase 1: Infraestructura Base
1. Crear migraciones para `sync_queue`, `sync_conflicts`, `network_status`
2. Crear modelos Eloquent
3. Implementar `NetworkChecker`
4. Implementar `OfflineService` básico

### Fase 2: Sincronización
1. Implementar `SyncOfflineOperationsJob`
2. Configurar scheduler para ejecutar job periódicamente
3. Implementar lógica de reintentos

### Fase 3: Integración
1. Modificar `PersonaIngresoSalidaService` para usar offline-first
2. Integrar con otros servicios críticos
3. Agregar middleware de auto-sincronización

### Fase 4: UI/UX
1. Crear componente de estado de sincronización
2. Agregar indicadores visuales
3. Crear vista de operaciones pendientes

### Fase 5: Manejo de Conflictos
1. Implementar `ConflictResolver`
2. Crear interfaz para resolución manual
3. Agregar notificaciones de conflictos

### Fase 6: Testing y Optimización
1. Pruebas de carga con muchas operaciones pendientes
2. Pruebas de conectividad intermitente
3. Optimización de queries y índices

## 14. Preguntas y Decisiones Pendientes

1. **¿Qué operaciones deben ser offline-first?**
   - ¿Solo escrituras o también lecturas críticas?
   - ¿Importaciones masivas deben ser offline-first?

2. **¿Cómo manejar validaciones que requieren datos del servidor?**
   - Ej: Verificar si persona ya tiene entrada registrada
   - Solución: Validar en sincronización y notificar si falla

3. **¿Qué hacer con operaciones que fallan después de max_retries?**
   - Notificar al administrador
   - Permitir resolución manual
   - Exportar para revisión

4. **¿Cómo manejar archivos grandes en importaciones offline?**
   - Guardar archivo localmente
   - Subir solo cuando hay internet estable

5. **¿Soporte para múltiples usuarios offline simultáneos?**
   - Cada usuario tiene su propia cola
   - Considerar límites de recursos

---

**Nota**: Este diseño es una propuesta inicial. Debe revisarse y ajustarse según necesidades específicas del proyecto.


