@php
    use Illuminate\Support\Str;
@endphp

<div class="row gy-4">
    <div class="col-12 col-xl-4 col-lg-5">
        <div class="card card-outline card-primary shadow-sm mb-4 mb-xl-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload me-2"></i> Cargar archivo
                </h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="iniciarImportacion" id="form-importacion">
                    <div class="form-group mb-3">
                        <label for="archivo_excel" class="form-label">Selecciona el archivo</label>
                        <div class="input-group">
                            <label for="archivo_excel" class="btn btn-outline-secondary mb-0" style="cursor: pointer;">
                                <i class="fas fa-folder-open me-1"></i> Examinar
                            </label>
                            <input type="file" class="d-none" id="archivo_excel" wire:model="archivo"
                                accept=".xlsx,.xls">
                            @php
                                $archivoClass = $archivoNombre !== 'Ningún archivo'
                                    ? 'text-success fw-bold'
                                    : 'text-muted';
                            @endphp
                            <input type="text" class="form-control {{ $archivoClass }}"
                                value="{{ $archivoNombre }}" readonly>
                            <span class="input-group-text" wire:loading wire:target="archivo">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                            </span>
                        </div>
                        <small class="form-text text-muted">
                            Formatos permitidos: XLSX o XLS. El archivo debe contener mínimo los campos básicos
                            para crear la persona.
                        </small>
                        @error('archivo')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row row-cols-1 row-cols-sm-2 g-2">
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100"
                                wire:loading.attr="disabled"
                                wire:target="iniciarImportacion,archivo"
                                @if ($archivoNombre === 'Ningún archivo') disabled @endif>
                                <span wire:loading.remove wire:target="iniciarImportacion,archivo">
                                    <i class="fas fa-play"></i> Iniciar importación
                                </span>
                                <span wire:loading wire:target="iniciarImportacion,archivo">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Procesando...
                                </span>
                            </button>
                        </div>
                        <div class="col">
                            @if ($plantillaDisponible)
                                <a href="{{ asset('storage/plantillas/personas_masivo.xlsx') }}"
                                    class="btn btn-outline-secondary w-100" download>
                                    <i class="fas fa-download"></i> Descargar plantilla
                                </a>
                            @else
                                <button type="button" class="btn btn-outline-secondary w-100" disabled
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="La plantilla no está disponible. Contacte al administrador del sistema.">
                                    <i class="fas fa-exclamation-triangle"></i> Plantilla no disponible
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (!$plantillaDisponible)
            <div class="alert alert-warning shadow-sm mb-4" role="alert">
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i> Plantilla no disponible
                </h5>
                <p class="mb-2">
                    La plantilla de importación no está disponible en este momento. Por favor, contacte al
                    administrador del sistema para obtenerla o utilice un archivo Excel con el formato correcto.
                </p>
                <hr>
                <p class="mb-0 small">
                    <strong>Formato requerido:</strong> El archivo debe contener las columnas estándar para la
                    importación de personas (Tipo de documento, número de documento, primer nombre, segundo nombre,
                    primer apellido, segundo apellido, email, celular).
                </p>
            </div>
        @endif

        <div class="callout callout-info shadow-sm mb-4">
            <h5 class="mb-3 text-primary">
                <i class="fas fa-lightbulb me-2"></i> Buenas prácticas
            </h5>
            <ul class="small ps-3 mb-0">
                <li>La primera fila debe incluir los encabezados estándares.</li>
                <li>Elimine filas en blanco y valide acentos o caracteres especiales.</li>
                <li>Verifique que el número de documento, el correo y el celular no estén repetidos.</li>
                <li>Los registros incompletos generarán una alerta para completar datos posteriormente.</li>
            </ul>
        </div>
    </div>

    <div class="col-12 col-xl-8 col-lg-7">
        @if ($mostrarProgreso)
            <div class="card card-outline card-success shadow-sm mb-3"
                wire:poll.2s="actualizarProgreso">
                <div
                    class="card-header d-flex flex-column flex-md-row
                    justify-content-md-between align-items-md-center gap-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks me-2"></i> Progreso de la importación
                    </h5>
                    <span class="badge bg-{{ $estadoColor }}">
                        {{ $estado }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-auto">
                                <span class="fw-bold">{{ $procesados }} / {{ $total }}</span>
                            </div>
                            <div class="col">
                                <div class="progress" style="height: 0.75rem;">
                                    @php
                                        $porcentaje = $total > 0 ? round(($procesados / $total) * 100) : 0;
                                    @endphp
                                    <div class="progress-bar progress-bar-striped
                                        {{ $estado === 'PROCESANDO...' ? 'progress-bar-animated' : '' }}"
                                        role="progressbar" style="width: {{ $porcentaje }}%"
                                        aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-auto text-md-right">
                                <span class="text-muted d-inline-block">
                                    @php
                                        $porcentaje = $total > 0 ? round(($procesados / $total) * 100) : 0;
                                    @endphp
                                    {{ $porcentaje }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-3 g-3 text-center">
                        <div class="col">
                            <div class="info-box bg-light shadow-none h-100">
                                <span class="info-box-icon bg-success text-white"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted text-uppercase small">Correctos</span>
                                    <span class="info-box-number h4 mb-0">{{ $exitosos }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="info-box bg-light shadow-none h-100">
                                <span class="info-box-icon bg-warning text-white"><i class="fas fa-clone"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted text-uppercase small">
                                        Duplicados / errores
                                    </span>
                                    <span class="info-box-number h4 mb-0 text-warning">{{ $duplicados }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="info-box bg-light shadow-none h-100">
                                <span class="info-box-icon bg-danger text-white">
                                    <i class="fas fa-phone-slash"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted text-uppercase small">
                                        Contactos incompletos
                                    </span>
                                    <span class="info-box-number h4 mb-0 text-danger">{{ $faltantes }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (!empty($issues))
                        <div class="mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    <strong>Incidencias detectadas</strong>
                                    <span class="badge bg-warning text-dark ms-2">{{ count($issues) }}</span>
                                </h6>
                            </div>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover table-bordered mb-0"
                                    aria-label="Tabla de incidencias de importación">
                                    <thead class="table-warning text-dark"
                                        style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th class="text-center" style="width: 80px;">Fila</th>
                                            <th>Tipo de incidencia</th>
                                            <th>Documento</th>
                                            <th>Email</th>
                                            <th>Celular</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($issues as $issue)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">
                                                        {{ $issue['row_number'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark px-2 py-1">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        {{ $issue['issue_type'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <code class="text-dark">
                                                        {{ $issue['numero_documento'] ?? '-' }}
                                                    </code>
                                                </td>
                                                <td>
                                                    @if (!empty($issue['email']))
                                                        <span class="text-muted">
                                                            <i class="fas fa-envelope me-1"></i>
                                                            {{ $issue['email'] }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted fst-italic">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($issue['celular']))
                                                        <span class="text-muted">
                                                            <i class="fas fa-phone me-1"></i>
                                                            {{ $issue['celular'] }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted fst-italic">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info mt-3 mb-0" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>
                                    Se muestran las primeras <strong>{{ count($issues) }}</strong> incidencias.
                                    Revise el archivo completo para ver todas las incidencias.
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="card card-outline card-warning shadow-sm mb-3">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col-12 col-md-auto">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> Incidencias recientes
                        </h5>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                            <select class="form-select form-select-sm" id="select-importacion-activa"
                                style="min-width: 200px;" wire:model="importacionSeleccionada"
                                @if (empty($importacionesActivas)) disabled @endif>
                                <option value="">Selecciona importación</option>
                                @foreach ($importacionesActivas as $activa)
                                    <option value="{{ $activa['id'] }}">
                                        #{{ $activa['id'] }} · {{ $activa['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="btn-group btn-group-sm" role="group">
                                @php
                                    $puedeDetener = $importacionId && in_array($estado, ['PENDIENTE', 'PROCESANDO...']);
                                @endphp
                                <button class="btn btn-outline-danger" wire:click="detenerImportacion"
                                    @if (!$puedeDetener) disabled @endif title="Detener y borrar"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="detenerImportacion">
                                        <i class="fas fa-stop-circle"></i>
                                    </span>
                                    <span wire:loading wire:target="detenerImportacion">
                                        <span class="spinner-border spinner-border-sm"></span>
                                    </span>
                                </button>
                                <button class="btn btn-outline-secondary" wire:click="recargarHistorial"
                                    title="Actualizar">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0"
                        aria-describedby="historial-importaciones-caption">
                        <caption id="historial-importaciones-caption" class="sr-only">
                            Historial de importaciones realizadas con detalle de archivo, fecha y estado
                        </caption>
                        <thead class="table-light">
                            <tr>
                                <th>Archivo</th>
                                <th>Fecha</th>
                                <th>Procesados</th>
                                <th>Duplicados</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($importaciones as $importacion)
                                <tr>
                                    <td class="text-truncate" style="max-width: 240px;"
                                        title="{{ $importacion['original_name'] ?? '' }}">
                                        <i class="far fa-file-excel text-success me-1"></i>
                                        {{ $importacion['original_name'] ?? '' }}
                                    </td>
                                    <td>
                                        @php
                                            $fecha = $importacion['created_at']
                                                ? \Carbon\Carbon::parse($importacion['created_at'])->format('d/m/Y H:i')
                                                : '-';
                                        @endphp
                                        {{ $fecha }}
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $importacion['success_count'] ?? 0 }}
                                        </span>
                                        <span class="text-muted">de {{ $importacion['total_rows'] ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ $importacion['duplicate_count'] ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $colorEstado = match ($importacion['status'] ?? '') {
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $colorEstado }}">
                                            {{ Str::upper($importacion['status'] ?? 'unknown') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            @php
                                                $nombreUsuario =
                                                    $importacion['user']['name'] ??
                                                    ($importacion['user']['persona']['primer_nombre'] ??
                                                        'Usuario desconocido');
                                            @endphp
                                            {{ $nombreUsuario }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $archivoNombreEliminar =
                                                $importacion['original_name'] ?? 'esta importación';
                                        @endphp
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-import"
                                            data-import-id="{{ $importacion['id'] }}"
                                            data-archivo-nombre="{{ $archivoNombreEliminar }}"
                                            title="Eliminar importación">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">Aún no hay importaciones
                                        registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Verificar cuando el archivo termine de subirse
    $wire.on('file-upload:finish', () => {
        console.log('Archivo subido completamente');
    });

    $wire.on('file-upload:error', (error) => {
        console.error('Error al subir archivo:', error);
        if (window.AlertHandler) {
            window.AlertHandler.showError('Error al cargar el archivo. Por favor, intenta de nuevo.');
        }
    });

    // Manejar clics en botones de eliminar
    function initEliminarButtons() {
        document.querySelectorAll('.btn-eliminar-import').forEach(function(btn) {
            if (btn.dataset.listenerAttached === 'true') {
                return;
            }
            btn.dataset.listenerAttached = 'true';

            btn.addEventListener('click', function() {
                const importId = this.dataset.importId;
                const nombreArchivo = this.dataset.archivoNombre || 'esta importación';

                if (window.AlertHandler) {
                    window.AlertHandler.showDeleteConfirmation(nombreArchivo).then((result) => {
                        if (result.isConfirmed) {
                            $wire.call('eliminarImportacion', importId);
                        }
                    });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Eliminar importación?',
                        html: `
                            <div class="text-center">
                                <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem;"></i>
                                <p class="mb-2">
                                    ¿Está seguro de eliminar <strong class="text-danger">${nombreArchivo}</strong>?
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Esta acción no se puede deshacer.
                                </small>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                        focusConfirm: false,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.call('eliminarImportacion', importId);
                        }
                    });
                } else {
                    const confirmMsg = '¿Está seguro de eliminar ' + nombreArchivo + '? ' +
                        'Esta acción no se puede deshacer.';
                    if (confirm(confirmMsg)) {
                        $wire.call('eliminarImportacion', importId);
                    }
                }
            });
        });
    }

    // Inicializar botones de eliminar
    initEliminarButtons();

    // Re-inicializar después de actualizaciones de Livewire
    $wire.on('importacion-eliminada', () => {
        setTimeout(initEliminarButtons, 100);
    });

    // Solo listeners de eventos de Livewire para notificaciones con SweetAlert2
    $wire.on('importacion-iniciada', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showInfoWithTimer(
                'Importación iniciada',
                data.message || 'El archivo se está procesando.'
            );
        }
    });

    $wire.on('importacion-completada', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showSuccessWithTimer(
                'Importación completada',
                data.message || 'La importación se completó exitosamente.'
            );
        }
    });

    $wire.on('importacion-fallida', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showError(
                data.message || 'La importación falló.',
                'Importación fallida'
            );
        }
    });

    $wire.on('error-importacion', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showError(
                data.message || 'Error al iniciar la importación.'
            );
        }
    });

    $wire.on('validation-failed', (errors) => {
        if (window.AlertHandler) {
            window.AlertHandler.showValidationErrors(errors);
        }
    });

    $wire.on('confirmar-detener', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showConfirmation(
                'Se cancelará el proceso y se eliminarán los registros asociados.',
                '¿Detener importación?'
            ).then((result) => {
                if (result.isConfirmed) {
                    $wire.call('confirmarDetener', data.importId);
                }
            });
        }
    });

    $wire.on('importacion-detendida', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showSuccessWithTimer(
                'Importación detenida',
                data.message || 'La importación fue detenida correctamente.'
            );
        }
    });

    $wire.on('error-detener', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showError(
                data.message || 'Error al detener la importación.'
            );
        }
    });

    $wire.on('importacion-eliminada', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showSuccessWithTimer(
                'Importación eliminada',
                data.message || 'La importación fue eliminada correctamente.'
            );
        }
    });

    $wire.on('error-eliminar', (data) => {
        if (window.AlertHandler) {
            window.AlertHandler.showError(
                data.message || 'Error al eliminar la importación.'
            );
        }
    });
</script>
@endscript
