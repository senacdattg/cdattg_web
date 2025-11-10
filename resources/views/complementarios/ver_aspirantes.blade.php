@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="mb-0"><i class="fas fa-users me-2"></i>Aspirantes - {{ $programa->nombre }}</h1>
            <p class="text-muted mb-0">Administre los aspirantes a programas de formación complementaria</p>
        </div>
        <div class="d-flex" style="gap: 1rem;">
            <button class="btn btn-primary" id="btn-nuevo-aspirante"
                @if(isset($existingProgress) && $existingProgress) disabled @endif
                onclick="console.log('Botón Nuevo Aspirante clickeado'); $('#modalNuevoAspirante').modal('show');">
                <i class="fas fa-plus me-1"></i>Nuevo Aspirante
            </button>
            <a href="{{ route('programas-complementarios.exportar-excel', $programa->id) }}"
                class="btn btn-success" id="btn-descargar-excel"
                @if(isset($existingProgress) && $existingProgress) style="pointer-events: none; opacity: 0.5;" @endif>
                <i class="fas fa-download me-1"></i>Descargar Excel
            </a>
            <a href="{{ route('programas-complementarios.descargar-cedulas', $programa->id) }}"
                class="btn btn-info" id="btn-descargar-cedulas"
                @if(isset($existingProgress) && $existingProgress) style="pointer-events: none; opacity: 0.5;" @endif>
                <i class="fas fa-file-pdf me-1"></i>Descargar Cédulas
            </a>
            <a href="{{ route('gestion-aspirantes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body pb-2">
            <form class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Buscar Aspirante</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control form-control-lg"
                            placeholder="Buscar por nombre o número de identidad">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-secondary btn-lg w-100">
                        <i class="fas fa-filter me-1"></i>Filtrar
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <button class="btn btn-outline-secondary btn-sm me-2">Todos</button>
                <button class="btn btn-outline-warning btn-sm me-2">En Proceso</button>
                <button class="btn btn-outline-success btn-sm me-2">Aceptados</button>
                <button class="btn btn-outline-danger btn-sm me-2">Rechazados</button>
                <button class="btn btn-outline-primary btn-sm" id="btn-validar-sofia"
                    data-programa-id="{{ $programa->id }}">
                    <i class="fas fa-search me-1"></i>Validar SenaSofiaPlus
                </button>
                <button class="btn btn-outline-info btn-sm" id="btn-validar-documento"
                    data-programa-id="{{ $programa->id }}">
                    <i class="fas fa-file-pdf me-1"></i>Validar con documento
                </button>
            </div>
        </div>
    </div>

    <div class="card mt-3 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Número de Identidad</th>
                            <th>Programa de Formación</th>
                            <th>Fecha Solicitud</th>
                            <th>Estado</th>
                            <th>SenaSofiaPlus</th>
                            <th>Condocumento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aspirantes as $index => $aspirante)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $aspirante->persona->primer_nombre }}
                                {{ $aspirante->persona->segundo_nombre ?? '' }}
                                {{ $aspirante->persona->primer_apellido }}
                                {{ $aspirante->persona->segundo_apellido ?? '' }}</td>
                            <td>{{ $aspirante->persona->numero_documento }}</td>
                            <td>{{ $aspirante->complementario->nombre }}</td>
                            <td>{{ $aspirante->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($aspirante->estado == 1)
                                    <span class="badge bg-warning text-dark">EN PROCESO</span>
                                @elseif($aspirante->estado == 2)
                                    <span class="badge bg-danger">RECHAZADO</span>
                                @elseif($aspirante->estado == 3)
                                    <span class="badge bg-success">ACEPTADO</span>
                                @else
                                    <span class="badge bg-secondary">DESCONOCIDO</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $aspirante->persona->estado_sofia_badge_class }}">
                                {{ $aspirante->persona->estado_sofia_label }}</span></td>
                            <td>
                                @if($aspirante->persona->condocumento == 1)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Subido
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle me-1"></i>No subido
                                    </span>
                                @endif
                            </td>
                            <td>
                                <!-- <button class="btn btn-warning btn-sm me-1 aspirante-action-btn" title="Editar"
                                    @if(isset($existingProgress) && $existingProgress) disabled @endif>
                                    <i class="fas fa-edit"></i>
                                </button> -->
                                <button class="btn btn-danger btn-sm aspirante-action-btn" title="Rechazar"
                                    data-aspirante-id="{{ $aspirante->id }}"
                                    @if(isset($existingProgress) && $existingProgress) disabled @endif>
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No hay aspirantes registrados para este programa.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <nav class="mt-3 d-flex justify-content-center">
                <ul class="pagination mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal Nuevo Aspirante -->
    <div class="modal fade" id="modalNuevoAspirante" tabindex="-1"
        aria-labelledby="modalNuevoAspiranteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoAspiranteLabel">
                        <i class="fas fa-plus me-2"></i>Agregar Nuevo Aspirante
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoAspirante">
                        <div class="mb-3">
                            <label for="numero_documento" class="form-label fw-bold">
                                <i class="fas fa-id-card me-1"></i>Número de Documento
                            </label>
                            <input type="text" class="form-control form-control-lg" id="numero_documento"
                                   placeholder="Ingrese el número de documento" required>
                            <div class="form-text">
                                Ingrese el número de documento de la persona que desea agregar como aspirante.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnAgregarAspirante">
                        <i class="fas fa-plus me-1"></i>Agregar Aspirante
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('resources/css/complementario/ver_aspirantes.css') }}">
@stop

@section('js')
    <script>
        console.log('Módulo de gestión de aspirantes cargado');

        // Variables globales para el progreso
        let progressInterval = null;
        let currentProgressId = null;

        console.log('JavaScript cargado - verificando elementos...');
        console.log('Botón nuevo aspirante:', document.getElementById('btn-nuevo-aspirante'));
        console.log('Botón agregar aspirante:', document.getElementById('btnAgregarAspirante'));
        console.log('Modal:', document.getElementById('modalNuevoAspirante'));

        // Verificar progreso existente al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded ejecutado');

            @if(isset($existingProgress) && $existingProgress)
                console.log('Progreso existente encontrado, iniciando monitoreo...');
                currentProgressId = {{ $existingProgress->id }};
                startProgressMonitoring(currentProgressId);

                // Actualizar estado del botón y deshabilitar acciones
                updateUIForValidationInProgress();
            @endif

            // Configurar event listener para agregar aspirante
            console.log('Configurando event listener para btnAgregarAspirante...');
            const btnAgregar = document.getElementById('btnAgregarAspirante');
            if (btnAgregar) {
                btnAgregar.addEventListener('click', async function() {
                    console.log('Botón Agregar Aspirante clickeado desde DOMContentLoaded');
                    const numeroDocumento = document.getElementById('numero_documento').value.trim();
                    console.log('Número de documento:', numeroDocumento);

                    if (!numeroDocumento) {
                        showAlert('error', 'Por favor ingrese un número de documento.');
                        return;
                    }

                    // Deshabilitar botón mientras procesa
                    const button = this;
                    const originalText = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Agregando...';

                    try {
                        const response = await fetch(
                            `/programas-complementarios/{{ $programa->id }}/agregar-aspirante`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            },
                            body: JSON.stringify({
                                numero_documento: numeroDocumento
                            })
                        });

                        const data = await response.json();
                        console.log('Respuesta del servidor:', data);

                        if (data.success) {
                            showAlert('success', data.message);

                            // Cerrar modal y limpiar formulario
                            $('#modalNuevoAspirante').modal('hide');
                            document.getElementById('numero_documento').value = '';

                            // Recargar la página para mostrar el nuevo aspirante
                            setTimeout(() => {
                                location.reload();
                            }, 1500);

                        } else {
                            showAlert('error', data.message);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        showAlert('error', 'Error de conexión. Intente nuevamente.');
                    } finally {
                        // Restaurar botón
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                });
                console.log('Event listener configurado exitosamente');
            } else {
                console.error('No se encontró el botón btnAgregarAspirante');
            }
        });

        // Función para actualizar UI durante validación
        function updateUIForValidationInProgress() {
            // Deshabilitar botón de validación SenaSofiaPlus
            const validationButton = document.getElementById('btn-validar-sofia');
            if (validationButton) {
                validationButton.disabled = true;
                validationButton.innerHTML = '<i class="fas fa-clock me-1"></i>Procesando...';
            }

            // Deshabilitar botón de validación de documentos
            const documentoButton = document.getElementById('btn-validar-documento');
            if (documentoButton) {
                documentoButton.disabled = true;
                documentoButton.innerHTML = '<i class="fas fa-clock me-1"></i>Procesando...';
            }

            // Deshabilitar botón de nuevo aspirante
            const newAspirantButton = document.getElementById('btn-nuevo-aspirante');
            if (newAspirantButton) {
                newAspirantButton.disabled = true;
            }

            // Deshabilitar botones de acciones de aspirantes
            const actionButtons = document.querySelectorAll('.aspirante-action-btn');
            actionButtons.forEach(button => {
                button.disabled = true;
            });
        }

        // Botón de validación de documentos
        document.getElementById('btn-validar-documento').addEventListener('click', async function() {
            const button = this;
            const originalText = button.innerHTML;

            // Confirmar antes de validar
            if (!confirm('¿Está seguro de que desea validar los documentos de todos los aspirantes en Google Drive?')) {
                return;
            }

            // Deshabilitar botón y mostrar loading
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Validando...';

            try {
                // Obtener el ID del programa desde el data attribute
                const programaId = button.dataset.programaId;

                // Hacer la petición AJAX
                const response = await fetch(`/programas-complementarios/${programaId}/validar-documento`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('success', data.message);
                    
                    // Recargar la página para mostrar los cambios
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    // Mostrar mensaje de error
                    showAlert('error', data.message || 'Error durante la validación');
                    // Restaurar botón
                    button.disabled = false;
                    button.innerHTML = originalText;
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión. Intente nuevamente.');
                // Restaurar botón
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });

        // Botón de validación SenaSofiaPlus
        document.getElementById('btn-validar-sofia').addEventListener('click', async function() {
            const button = this;
            const originalText = button.innerHTML;

            // Deshabilitar botón y mostrar loading
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Iniciando validación...';

            try {
                // Obtener el ID del programa desde el data attribute
                const programaId = button.dataset.programaId;

                // Hacer la petición AJAX
                const response = await fetch(`/programas-complementarios/${programaId}/validar-sofia`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    currentProgressId = data.progress_id;
                    showAlert('success', data.message);

                    // Iniciar monitoreo del progreso
                    startProgressMonitoring(currentProgressId);

                    // Cambiar texto del botón y deshabilitar acciones
                    button.innerHTML = '<i class="fas fa-clock me-1"></i>Procesando...';
                    updateUIForValidationInProgress();

                } else {
                    // Mostrar mensaje de error
                    showAlert('error', data.message || 'Error durante la validación');
                    // Restaurar botón
                    button.disabled = false;
                    button.innerHTML = originalText;
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión. Intente nuevamente.');
                // Restaurar botón
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });

        // Función para monitorear el progreso
        function startProgressMonitoring(progressId) {
            // Actualizar cada 3 segundos
            progressInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/sofia-validation-progress/${progressId}`);
                    const data = await response.json();

                    if (data.success) {
                        const progress = data.progress;

                        // Actualizar barra de progreso si existe
                        updateProgressDisplay(progress);

                        // Si está completado o fallido, detener monitoreo
                        if (progress.status === 'completed' || progress.status === 'failed') {
                            clearInterval(progressInterval);
                            handleValidationComplete(progress);
                        }
                    }
                } catch (error) {
                    console.error('Error monitoreando progreso:', error);
                }
            }, 3000);
        }

        // Función para actualizar la visualización del progreso
        function updateProgressDisplay(progress) {
            // Buscar o crear contenedor de progreso
            let progressContainer = document.getElementById('sofia-progress-container');
            if (!progressContainer) {
                progressContainer = document.createElement('div');
                progressContainer.id = 'sofia-progress-container';
                progressContainer.className = 'mt-3';

                // Insertar después del botón de filtros
                const buttonContainer = document.querySelector('.mt-3');
                buttonContainer.appendChild(progressContainer);
            }

            // Calcular estadísticas adicionales
            const remaining = progress.total_aspirantes - progress.processed_aspirantes;
            const successRate = progress.processed_aspirantes > 0 ?
                Math.round((progress.successful_validations / progress.processed_aspirantes) * 100) : 0;
            const estimatedTimeRemaining = calculateEstimatedTime(progress);

            // Determinar color de la barra basado en el estado
            const progressBarClass = progress.status === 'failed' ? 'bg-danger' :
                                   progress.status === 'completed' ? 'bg-success' : 'bg-info';

            progressContainer.innerHTML = `
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">
                                <i class="fas fa-cog ${progress.status === 'processing' ? 'fa-spin' : ''} me-2"></i>
                                Validación SenaSofiaPlus - ${progress.status_label}
                            </h6>
                            <small class="text-muted">${progress.progress_percentage}%</small>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar ${progressBarClass}" role="progressbar"
                                  style="width: ${progress.progress_percentage}%"
                                  aria-valuenow="${progress.progress_percentage}"
                                  aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row text-center mb-2">
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Total</small>
                                <strong>${progress.total_aspirantes}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-success d-block">Exitosos</small>
                                <strong class="text-success">${progress.successful_validations}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-danger d-block">Errores</small>
                                <strong class="text-danger">${progress.failed_validations}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-info d-block">Pendientes</small>
                                <strong class="text-info">${remaining}</strong>
                            </div>
                        </div>
                        ${progress.status === 'processing' ? `
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Tasa de éxito: ${successRate}%
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        ${estimatedTimeRemaining}
                                    </small>
                                </div>
                            </div>
                        ` : ''}
                        ${progress.started_at ? `
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Iniciado: ${new Date(progress.started_at).toLocaleString('es-CO')}
                                </small>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        // Función para calcular tiempo estimado restante
        function calculateEstimatedTime(progress) {
            if (progress.status !== 'processing' || progress.processed_aspirantes === 0) {
                return 'Calculando...';
            }

            const elapsed = (new Date() - new Date(progress.started_at)) / 1000; // segundos
            const avgTimePerAspirante = elapsed / progress.processed_aspirantes;
            const remaining = progress.total_aspirantes - progress.processed_aspirantes;
            const estimatedSeconds = avgTimePerAspirante * remaining;

            if (estimatedSeconds < 60) {
                return `~${Math.round(estimatedSeconds)}s restantes`;
            } else if (estimatedSeconds < 3600) {
                return `~${Math.round(estimatedSeconds / 60)}min restantes`;
            } else {
                return `~${Math.round(estimatedSeconds / 3600)}h restantes`;
            }
        }

        // Función para manejar cuando la validación se completa
        function handleValidationComplete(progress) {
            const button = document.getElementById('btn-validar-sofia');
            const progressContainer = document.getElementById('sofia-progress-container');

            if (progress.status === 'completed') {
                const successRate = progress.total_aspirantes > 0 ?
                    Math.round((progress.successful_validations / progress.total_aspirantes) * 100) : 0;

                showAlert('success',
                    `Validación completada. ${progress.successful_validations}/${progress.total_aspirantes}
                        aspirantes validados exitosamente (${successRate}%).`
                );

                // Cambiar botón a completado
                button.innerHTML = '<i class="fas fa-check-circle me-1"></i>Completado';
                button.className = 'btn btn-success btn-sm';

                // Recargar página después de 3 segundos
                setTimeout(() => {
                    location.reload();
                }, 3000);

            } else if (progress.status === 'failed') {
                let errorMessage = 'La validación falló. ';
                if (progress.errors && progress.errors.length > 0) {
                    errorMessage += `Errores encontrados: ${progress.errors.length}. `;
                    errorMessage += 'Revisa los logs del servidor para más detalles.';
                } else {
                    errorMessage += 'Revisa los logs del servidor para más detalles.';
                }

                showAlert('error', errorMessage);

                // Mostrar detalles de errores si existen
                if (progress.errors && progress.errors.length > 0) {
                    showErrorDetails(progress.errors);
                }

                // Restaurar botón y habilitar acciones
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-search me-1"></i>Validar SenaSofiaPlus';
                restoreUIAfterValidation();
            }

            // Ocultar contenedor de progreso después de 5 segundos
            if (progressContainer) {
                setTimeout(() => {
                    progressContainer.remove();
                }, 5000);
            }
        }

        // Función para mostrar detalles de errores
        function showErrorDetails(errors) {
            const errorContainer = document.createElement('div');
            errorContainer.className = 'mt-3 alert alert-danger';
            errorContainer.innerHTML = `
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Detalles de Errores:</h6>
                <div style="max-height: 200px; overflow-y: auto;">
                    <ul class="mb-0">
                        ${errors.slice(0, 10).map(error => `<li style="font-size: 0.875rem;">${error}</li>`).join('')}
                        ${errors.length > 10 ?
                            `<li style="font-size: 0.875rem;">... y ${errors.length - 10} errores más</li>` : ''}
                    </ul>
                </div>
            `;

            const progressContainer = document.getElementById('sofia-progress-container');
            if (progressContainer) {
                progressContainer.appendChild(errorContainer);
            }
        }

        // Función para restaurar UI después de validación
        function restoreUIAfterValidation() {
            // Habilitar botón de validación SenaSofiaPlus
            const validationButton = document.getElementById('btn-validar-sofia');
            if (validationButton) {
                validationButton.disabled = false;
                validationButton.innerHTML = '<i class="fas fa-search me-1"></i>Validar SenaSofiaPlus';
            }

            // Habilitar botón de validación de documentos
            const documentoButton = document.getElementById('btn-validar-documento');
            if (documentoButton) {
                documentoButton.disabled = false;
                documentoButton.innerHTML = '<i class="fas fa-file-pdf me-1"></i>Validar con documento';
            }

            // Habilitar botón de nuevo aspirante
            const newAspirantButton = document.getElementById('btn-nuevo-aspirante');
            if (newAspirantButton) {
                newAspirantButton.disabled = false;
            }

            // Habilitar botones de acciones de aspirantes
            const actionButtons = document.querySelectorAll('.aspirante-action-btn');
            actionButtons.forEach(button => {
                button.disabled = false;
            });
        }


        // Event listener para botones de rechazar aspirante
        document.addEventListener('click', function(e) {
            const button = e.target.closest('.aspirante-action-btn');
            if (button && button.dataset.aspiranteId) {
                e.preventDefault();
                const aspiranteId = button.dataset.aspiranteId;

                if (!aspiranteId) {
                    showAlert('error', 'ID del aspirante no encontrado.');
                    return;
                }

                // Confirmación antes de rechazar
                if (!confirm('¿Está seguro de que desea rechazar este aspirante del programa? ' +
                    'El aspirante será marcado como rechazado.')) {
                    return;
                }

                // Deshabilitar botón mientras procesa
                const originalHTML = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                // Hacer petición AJAX
                fetch(`/programas-complementarios/{{ $programa->id }}/aspirante/${aspiranteId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        // Recargar la página para actualizar la tabla
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showAlert('error', data.message);
                        // Restaurar botón
                        button.disabled = false;
                        button.innerHTML = originalHTML;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error de conexión. Intente nuevamente.');
                    // Restaurar botón
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                });
            }
        });

        // Función para mostrar alertas
        function showAlert(type, message) {
            // Crear elemento de alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}
                alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Agregar al DOM
            document.body.appendChild(alertDiv);

            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>

    <!-- CSRF Token para AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop