@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="mb-0"><i class="fas fa-users me-2"></i>Aspirantes - {{ $programa->nombre }}</h1>
            <p class="text-muted mb-0">Administre los aspirantes a programas de formación complementaria</p>
        </div>
        <a href="{{ route('gestion-aspirantes') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body pb-2">
            <form class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Buscar Aspirante</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control form-control-lg" placeholder="Buscar por nombre o número de identidad">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Programa</label>
                    <select class="form-select form-select-lg">
                        <option selected>{{ $programa->nombre }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Año</label>
                    <select class="form-select form-select-lg">
                        <option selected>Todos los años</option>
                        <option>2025</option>
                        <option>2024</option>
                        <option>2023</option>
                        <option>2022</option>
                        <option>2021</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-plus me-1"></i>Nuevo Aspirante
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <button class="btn btn-outline-secondary btn-sm me-2">Todos</button>
                <button class="btn btn-outline-warning btn-sm me-2">En Proceso</button>
                <button class="btn btn-outline-success btn-sm me-2">Aceptados</button>
                <button class="btn btn-outline-danger btn-sm me-2">Rechazados</button>
                <button class="btn btn-outline-primary btn-sm" id="btn-validar-sofia" data-programa-id="{{ $programa->id }}">
                    <i class="fas fa-search me-1"></i>Validar SenaSofiaPlus
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aspirantes as $index => $aspirante)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $aspirante->persona->primer_nombre }} {{ $aspirante->persona->segundo_nombre ?? '' }} {{ $aspirante->persona->primer_apellido }} {{ $aspirante->persona->segundo_apellido ?? '' }}</td>
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
                            <td><span class="badge {{ $aspirante->persona->estado_sofia_badge_class }}">{{ $aspirante->persona->estado_sofia_label }}</span></td>
                            <td>
                                <a href="{{ route('programas-complementarios.perfil-aspirante', $aspirante->id) }}" class="btn btn-info btn-sm me-1" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-warning btn-sm me-1" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">
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
@stop

@section('css')
    <style>
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .badge.bg-success {
            background-color: #28a745 !important;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
        }
        .btn-info {
            background-color: #17a2b8 !important;
            border: none;
            color: #fff;
        }
        .btn-warning {
            background-color: #ffc107 !important;
            border: none;
            color: #212529;
        }
        .btn-danger {
            background-color: #dc3545 !important;
            border: none;
            color: #fff;
        }
        .btn-primary {
            background-color: #0d6efd !important;
            border: none;
        }
        .btn-outline-secondary {
            border-color: #ced4da;
            color: #495057;
        }
        .btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
        }
        .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
        }
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        .card {
            border-radius: 0.75rem;
        }
        .input-group-text {
            background: #fff;
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Módulo de gestión de aspirantes cargado');

        // Variables globales para el progreso
        let progressInterval = null;
        let currentProgressId = null;

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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    currentProgressId = data.progress_id;
                    showAlert('success', data.message);

                    // Iniciar monitoreo del progreso
                    startProgressMonitoring(currentProgressId);

                    // Cambiar texto del botón
                    button.innerHTML = '<i class="fas fa-clock me-1"></i>Procesando...';

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
                    `Validación completada. ${progress.successful_validations}/${progress.total_aspirantes} aspirantes validados exitosamente (${successRate}%).`
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

                // Restaurar botón
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-search me-1"></i>Validar SenaSofiaPlus';
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
                        ${errors.length > 10 ? `<li style="font-size: 0.875rem;">... y ${errors.length - 10} errores más</li>` : ''}
                    </ul>
                </div>
            `;

            const progressContainer = document.getElementById('sofia-progress-container');
            if (progressContainer) {
                progressContainer.appendChild(errorContainer);
            }
        }

        // Función para mostrar alertas
        function showAlert(type, message) {
            // Crear elemento de alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
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