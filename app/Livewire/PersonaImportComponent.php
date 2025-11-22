<?php

namespace App\Livewire;

use App\Configuration\UploadLimits;
use App\Exceptions\ImportFileTempPathException;
use App\Jobs\ProcessPersonaImportJob;
use App\Models\PersonaImport;
use App\Services\PersonaImportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class PersonaImportComponent extends Component
{
    use WithFileUploads;

    private const ARCHIVO_POR_DEFECTO = 'Ningún archivo';
    private const PATRON_PAYLOAD_IMPORT_ID = '%"importId";i:';

    public $archivo;
    public $archivoNombre = self::ARCHIVO_POR_DEFECTO;
    public $mostrarProgreso = false;
    public $importacionId = null;
    public $importacionSeleccionada = null;
    public $importaciones = [];
    public $importacionesActivas = [];
    public $plantillaDisponible = false;

    // Estado de progreso
    public $procesados = 0;
    public $total = 0;
    public $exitosos = 0;
    public $duplicados = 0;
    public $faltantes = 0;
    public $estado = 'PENDIENTE';
    public $estadoColor = 'secondary';
    public $issues = [];

    protected $listeners = ['actualizarProgreso', 'recargarHistorial'];

    public function mount()
    {
        $this->cargarImportaciones();
        $this->procesarPendientes();
        $this->verificarPlantilla();
    }

    /**
     * Verifica si la plantilla de importación está disponible
     */
    private function verificarPlantilla(): void
    {
        $rutaPlantilla = public_path('storage/plantillas/personas_masivo.xlsx');
        $this->plantillaDisponible = file_exists($rutaPlantilla);
    }

    /**
     * Procesa importaciones pendientes si el queue está en modo sync
     * o si hay importaciones que quedaron pendientes sin procesar
     */
    private function procesarPendientes(): void
    {
        $pendientes = PersonaImport::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24))
            ->get();

        foreach ($pendientes as $importacion) {
            // Si el queue está en sync, procesar directamente
            if (config('queue.default') === 'sync') {
                try {
                    $importService = app(PersonaImportService::class);
                    $importService->procesar($importacion);
                } catch (\Throwable $e) {
                    // Si falla, dejar que el job lo maneje
                    ProcessPersonaImportJob::dispatch($importacion->id);
                }
            } else {
                // Si no está en sync, verificar si el job existe en la cola
                $jobExists = DB::table('jobs')
                    ->where('queue', 'long-running')
                    ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID . $importacion->id . '%')
                    ->exists();

                // Si no existe el job, crearlo
                if (!$jobExists) {
                    ProcessPersonaImportJob::dispatch($importacion->id);

                    // Si la importación tiene más de 10 segundos en pending, procesar directamente
                    // (indica que no hay worker corriendo)
                    if ($importacion->created_at->diffInSeconds(now()) > 10) {
                        try {
                            $importService = app(PersonaImportService::class);
                            $importService->procesar($importacion);
                        } catch (\Throwable $e) {
                            Log::error('Error procesando importación pendiente', [
                                'import_id' => $importacion->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function cargarImportaciones()
    {
        $this->importaciones = PersonaImport::query()
            ->with(['user.persona'])
            ->withCount('issues')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get()
            ->toArray();

        $this->importacionesActivas = PersonaImport::query()
            ->whereIn('status', ['pending', 'processing'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($import) {
                return [
                    'id' => $import->id,
                    'nombre' => Str::limit($import->original_name, 38),
                ];
            })
            ->toArray();
    }

    public function updatedArchivo()
    {
        Log::info('updatedArchivo llamado', [
            'archivo' => $this->archivo ? 'presente' : 'ausente',
        ]);
        
        if ($this->archivo) {
            $this->archivoNombre = $this->archivo->getClientOriginalName();
            Log::info('Archivo actualizado', [
                'nombre' => $this->archivoNombre,
            ]);
        } else {
            $this->archivoNombre = self::ARCHIVO_POR_DEFECTO;
        }
    }


    public function iniciarImportacion()
    {
        Log::info('iniciarImportacion llamado', [
            'archivo' => $this->archivo ? 'presente' : 'ausente',
            'archivoNombre' => $this->archivoNombre,
            'archivo_tipo' => $this->archivo ? get_class($this->archivo) : 'null',
        ]);

        // Validar que se haya seleccionado un archivo
        if (!$this->archivo) {
            Log::warning('No hay archivo seleccionado', [
                'archivoNombre' => $this->archivoNombre,
            ]);
            $this->dispatch('error-importacion', [
                'message' => 'Debes seleccionar un archivo para importar.',
            ]);
            return;
        }

        // Esperar un momento para asegurar que el archivo esté completamente cargado
        if (!$this->archivo->getRealPath()) {
            Log::warning('Archivo sin ruta temporal', [
                'archivoNombre' => $this->archivoNombre,
            ]);
            $this->dispatch('error-importacion', [
                'message' => 'El archivo aún se está cargando. Por favor, espera un momento e intenta de nuevo.',
            ]);
            return;
        }

        $this->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:' . (UploadLimits::IMPORT_CONTENT_LENGTH_BYTES / 1024),
        ], [
            'archivo.required' => 'Debes seleccionar un archivo para importar.',
            'archivo.mimes' => 'El archivo debe ser de tipo XLSX o XLS.',
            'archivo.max' => 'El archivo no puede superar los ' .
                (UploadLimits::IMPORT_CONTENT_LENGTH_BYTES / 1024 / 1024) . 'MB.',
        ]);

        try {
            // Verificar que el archivo temporal existe
            if (!$this->archivo->getRealPath()) {
                throw new ImportFileTempPathException();
            }

            // Livewire guarda el archivo automáticamente, obtener la ruta temporal
            $tempPath = $this->archivo->getRealPath();

            // Crear un UploadedFile desde el archivo temporal de Livewire
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $this->archivo->getClientOriginalName(),
                $this->archivo->getMimeType(),
                null,
                true
            );

            $importService = app(PersonaImportService::class);
            $import = $importService->iniciarImportacion(
                $uploadedFile,
                Auth::id()
            );

            $this->importacionId = $import->id;
            $this->importacionSeleccionada = $import->id;
            $this->mostrarProgreso = true;
            $this->resetearProgreso();
            $this->archivo = null;
            $this->archivoNombre = self::ARCHIVO_POR_DEFECTO;

            // Limpiar estado previo en sesión
            session()->forget('import_status_' . $import->id);

            $this->dispatch('importacion-iniciada', [
                'message' => 'Importación iniciada correctamente.',
            ]);

            // Iniciar polling
            $this->actualizarProgreso();
        } catch (\Throwable $e) {
            $this->dispatch('error-importacion', [
                'message' => 'Error al iniciar la importación: ' . $e->getMessage(),
            ]);
        }
    }

    public function actualizarProgreso()
    {
        if (!$this->importacionId) {
            return;
        }

        // Refrescar el modelo desde la base de datos para obtener los últimos datos
        $importacion = PersonaImport::find($this->importacionId);

        if (!$importacion) {
            $this->mostrarProgreso = false;
            return;
        }

        // Refrescar el modelo para obtener los datos más recientes
        $importacion->refresh();

        // Si está en pending por más de 5 segundos, procesar directamente
        // (indica que el worker no está escuchando la cola correcta)
        if ($importacion->status === 'pending' && $importacion->created_at->diffInSeconds(now()) > 5) {
            $jobExists = DB::table('jobs')
                ->where('queue', 'long-running')
                ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID . $importacion->id . '%')
                ->exists();

            // Si el job existe en la cola pero no se está procesando, procesar directamente
            if ($jobExists) {
                try {
                    $importService = app(PersonaImportService::class);
                    $importService->procesar($importacion);
                    $importacion->refresh();
                } catch (\Throwable $e) {
                    Log::error('Error procesando importación automáticamente', [
                        'import_id' => $importacion->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->procesados = $importacion->processed_rows ?? 0;
        $this->total = $importacion->total_rows ?? 0;
        
        // Si total es 0 pero hay procesados, usar procesados como estimación
        if ($this->total === 0 && $this->procesados > 0) {
            $this->total = $this->procesados;
        }
        
        $this->exitosos = $importacion->success_count ?? 0;
        $this->duplicados = $importacion->duplicate_count ?? 0;
        $this->faltantes = $importacion->missing_contact_count ?? 0;

        $statusLabels = [
            'pending' => 'PENDIENTE',
            'processing' => 'PROCESANDO...',
            'completed' => 'COMPLETADO',
            'failed' => 'FALLIDO',
        ];

        $statusColors = [
            'pending' => 'secondary',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
        ];

        $this->estado = $statusLabels[$importacion->status] ?? strtoupper($importacion->status);
        $this->estadoColor = $statusColors[$importacion->status] ?? 'secondary';

        // Cargar issues solo si hay cambios o si no están cargados
        $this->issues = $importacion->issues()
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($issue) {
                return [
                    'row_number' => $issue->row_number,
                    'issue_type' => $this->traducirIssueType($issue->issue_type, $issue->error_message),
                    'numero_documento' => $issue->numero_documento,
                    'email' => $issue->email,
                    'celular' => $issue->celular,
                ];
            })
            ->toArray();

        // Si está completado o fallido, detener polling
        if (in_array($importacion->status, ['completed', 'failed'])) {
            $this->mostrarProgreso = false;

            // Disparar evento solo si no se ha disparado antes (evitar bucle)
            $estadoAnterior = session('import_status_' . $importacion->id);

            if ($estadoAnterior !== $importacion->status) {
                session(['import_status_' . $importacion->id => $importacion->status]);

                if ($importacion->status === 'completed') {
                    $this->dispatch('importacion-completada', [
                        'message' => 'La importación se completó exitosamente.',
                    ]);
                } elseif ($importacion->status === 'failed') {
                    $this->dispatch('importacion-fallida', [
                        'message' => $importacion->error_message ?? 'La importación falló.',
                    ]);
                }
            }

            $this->cargarImportaciones();
        }
    }

    public function updatedImportacionSeleccionada($value)
    {
        if ($value) {
            $this->seleccionarImportacion($value);
        }
    }

    public function seleccionarImportacion($importId)
    {
        if (!$importId) {
            return;
        }

        $this->importacionId = $importId;
        $this->importacionSeleccionada = $importId;
        $this->mostrarProgreso = true;
        $this->actualizarProgreso();
    }

    public function detenerImportacion()
    {
        if (!$this->importacionId) {
            return;
        }

        $this->dispatch('confirmar-detener', [
            'importId' => $this->importacionId,
        ]);
    }

    public function confirmarDetener($importId)
    {
        try {
            $importacion = PersonaImport::findOrFail($importId);

            DB::transaction(function () use ($importacion) {
                $importacion->issues()->delete();
                $importacion->contactAlerts()->delete();

                // Eliminar jobs antes de intentar eliminar el archivo
                DB::table('jobs')
                    ->where('queue', 'long-running')
                    ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID . $importacion->id . '%')
                    ->delete();

                DB::table('failed_jobs')
                    ->where('queue', 'long-running')
                    ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID . $importacion->id . '%')
                    ->delete();

                $importacion->delete();
            });

            // Intentar eliminar archivo después de la transacción (con reintentos)
            if ($importacion->path && $importacion->disk) {
                $this->eliminarArchivoConReintentos($importacion->disk, $importacion->path);
            }

            $this->importacionId = null;
            $this->importacionSeleccionada = null;
            $this->mostrarProgreso = false;
            $this->resetearProgreso();
            $this->cargarImportaciones();

            $this->dispatch('importacion-detendida', [
                'message' => 'La importación fue detenida y eliminada correctamente.',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('error-detener', [
                'message' => 'Error al detener la importación: ' . $e->getMessage(),
            ]);
        }
    }

    public function recargarHistorial()
    {
        $this->cargarImportaciones();
        if ($this->importacionId) {
            $this->actualizarProgreso();
        }
    }

    public function eliminarImportacion($importId)
    {
        try {
            $importacion = PersonaImport::findOrFail($importId);

            DB::transaction(function () use ($importacion) {
                $importacion->issues()->delete();
                $importacion->contactAlerts()->delete();

                // Eliminar jobs antes de intentar eliminar el archivo
                DB::table('jobs')
                    ->where('queue', 'long-running')
                    ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID . $importacion->id . '%')
                    ->delete();

                DB::table('failed_jobs')
                    ->where('queue', 'long-running')
                    ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID . $importacion->id . '%')
                    ->delete();

                $importacion->delete();
            });

            // Intentar eliminar archivo después de la transacción (con reintentos)
            if ($importacion->path && $importacion->disk) {
                $this->eliminarArchivoConReintentos($importacion->disk, $importacion->path);
            }

            // Si era la importación seleccionada, limpiar
            if ($this->importacionId === $importId) {
                $this->importacionId = null;
                $this->importacionSeleccionada = null;
                $this->mostrarProgreso = false;
                $this->resetearProgreso();
            }

            $this->cargarImportaciones();

            $this->dispatch('importacion-eliminada', [
                'message' => 'La importación fue eliminada correctamente.',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('error-eliminar', [
                'message' => 'Error al eliminar la importación: ' . $e->getMessage(),
            ]);
        }
    }

    private function resetearProgreso()
    {
        $this->procesados = 0;
        $this->total = 0;
        $this->exitosos = 0;
        $this->duplicados = 0;
        $this->faltantes = 0;
        $this->estado = 'PENDIENTE';
        $this->estadoColor = 'secondary';
        $this->issues = [];
    }

    /**
     * Intenta eliminar un archivo con reintentos y liberación de recursos
     */
    private function eliminarArchivoConReintentos(string $disk, string $path, int $intentos = 3): void
    {
        // Forzar liberación de recursos
        gc_collect_cycles();
        
        for ($i = 0; $i < $intentos; $i++) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                    return; // Eliminación exitosa
                }
                return; // Archivo no existe
            } catch (\Throwable $e) {
                Log::warning('Intento de eliminación de archivo falló', [
                    'intento' => $i + 1,
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
                
                if ($i < $intentos - 1) {
                    // Esperar y forzar GC antes del siguiente intento
                    usleep(500000); // 0.5 segundos
                    gc_collect_cycles();
                } else {
                    // Último intento falló - loguear como advertencia
                    Log::warning('No se pudo eliminar el archivo después de ' . $intentos . ' intentos', [
                        'path' => $path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    private function traducirIssueType(?string $issueType, ?string $errorMessage = null): string
    {
        $labels = [
            'missing_document' => 'Documento de identidad ausente',
            'missing_document_type' => 'Tipo de documento no válido o no encontrado',
            'missing_required_fields' => 'Faltan nombres o apellidos obligatorios',
            'duplicate_document_in_file' => 'Documento repetido en el archivo',
            'duplicate_document_existing' => 'Documento ya registrado en el sistema',
            'duplicate_email_in_file' => 'Correo repetido en el archivo',
            'duplicate_email_existing' => 'Correo electrónico ya registrado en el sistema',
            'duplicate_celular_in_file' => 'Celular repetido en el archivo',
            'duplicate_celular_existing' => 'Número de celular ya registrado en el sistema',
            'duplicate_telefono_in_file' => 'Teléfono repetido en el archivo',
            'duplicate_telefono_existing' => 'Número de teléfono ya registrado en el sistema',
            'duplicate_generic' => 'Datos duplicados en el sistema',
            'partial_import_email' => 'Importado sin email (ya existe en el sistema)',
            'partial_import_celular' => 'Importado sin celular (ya existe en el sistema)',
            'partial_import_telefono' => 'Importado sin teléfono (ya existe en el sistema)',
            'partial_import_email_celular' => 'Importado sin email y celular (ya existen en el sistema)',
            'partial_import_email_telefono' => 'Importado sin email y teléfono (ya existen en el sistema)',
            'partial_import_celular_telefono' => 'Importado sin celular y teléfono (ya existen en el sistema)',
            'partial_import_email_celular_telefono' => 'Importado sin contactos (email, celular y teléfono ya existen)',
        ];

        if ($issueType === null) {
            return 'Incidencia sin clasificar';
        }

        // Si es un error persistente, intentar detectar el tipo de duplicado
        if ($issueType === 'persist_error' && $errorMessage) {
            $detectedType = $this->detectarTipoErrorDesdeMensaje($errorMessage);
            if ($detectedType && isset($labels[$detectedType])) {
                return $labels[$detectedType];
            }
            return 'Error al guardar el registro. Verifique que los datos no estén duplicados.';
        }

        return $labels[$issueType] ?? Str::headline(str_replace('_', ' ', $issueType));
    }

    /**
     * Detecta el tipo de error de duplicado desde el mensaje de error SQL
     */
    private function detectarTipoErrorDesdeMensaje(string $errorMessage): ?string
    {
        $tipoError = null;

        // Detectar errores de email duplicado
        if (str_contains($errorMessage, 'personas_email_unique') ||
            (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, '@'))) {
            $tipoError = 'duplicate_email_existing';
        } elseif (str_contains($errorMessage, 'personas_numero_documento_unique') ||
            (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, 'numero_documento'))) {
            // Detectar errores de documento duplicado
            $tipoError = 'duplicate_document_existing';
        } elseif (str_contains($errorMessage, 'personas_celular_unique') ||
            (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, 'celular'))) {
            // Detectar errores de celular duplicado
            $tipoError = 'duplicate_celular_existing';
        } elseif (str_contains($errorMessage, 'personas_telefono_unique') ||
            (str_contains($errorMessage, 'Duplicate entry') && str_contains($errorMessage, 'telefono'))) {
            // Detectar errores de teléfono duplicado
            $tipoError = 'duplicate_telefono_existing';
        } elseif (str_contains($errorMessage, 'Duplicate entry')) {
            // Error genérico de duplicado
            $tipoError = 'duplicate_generic';
        }

        return $tipoError;
    }

    public function render()
    {
        return view('livewire.persona-import-component');
    }
}
