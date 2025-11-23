@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="mb-0"><i class="fas fa-users me-2"></i>Aspirantes - {{ $programa->nombre }}</h1>
            <p class="text-muted mb-0">Administre los aspirantes a programas de formación complementaria</p>
        </div>
        <div class="d-flex" style="gap: 1rem;">
            <button class="btn btn-primary" id="btn-agregar-aprendiz"
                @if(isset($existingProgress) && $existingProgress) disabled @endif
                onclick="$('#modalAgregarAprendiz').modal('show');">
                <i class="fas fa-user-plus me-1"></i>Agregar Aspirante
            </button>
            <a href="{{ route('aspirantes.exportar-excel', $programa->id) }}"
                class="btn btn-success" id="btn-descargar-excel"
                @if(isset($existingProgress) && $existingProgress) style="pointer-events: none; opacity: 0.5;" @endif>
                <i class="fas fa-download me-1"></i>Descargar Excel
            </a>
            <a href="{{ route('aspirantes.descargar-cedulas', $programa->id) }}"
                class="btn btn-info" id="btn-descargar-cedulas"
                @if(isset($existingProgress) && $existingProgress) style="pointer-events: none; opacity: 0.5;" @endif>
                <i class="fas fa-file-pdf me-1"></i>Descargar Cédulas
            </a>
            <a href="{{ route('aspirantes.index') }}" class="btn btn-outline-secondary">
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
                    <caption class="visually-hidden">Lista de aspirantes del programa {{ $programa->nombre }}</caption>
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

    <!-- Modal Agregar Aprendiz -->
    <div class="modal fade" id="modalAgregarAprendiz" tabindex="-1"
        aria-labelledby="modalAgregarAprendizLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarAprendizLabel">
                        <i class="fas fa-user-plus me-2"></i>Agregar Aprendiz
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario de búsqueda -->
                    <div id="busqueda-section">
                        <form id="formBuscarAprendiz">
                            <div class="mb-3">
                                <label for="numero_documento_buscar" class="form-label fw-bold">
                                    <i class="fas fa-id-card me-1"></i>Número de Documento
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" id="numero_documento_buscar"
                                           placeholder="Ingrese el número de documento" required>
                                    <button type="submit" class="btn btn-primary" id="btnBuscarAprendiz">
                                        <i class="fas fa-search me-1"></i>Buscar
                                    </button>
                                </div>
                                <div class="form-text">
                                    Ingrese el número de documento para buscar la persona en el sistema.
                                </div>
                            </div>
                        </form>
                        <div id="loading-busqueda" class="text-center d-none py-4">
                            <img src="{{ asset('dist/img/LogoSena.png') }}" alt="Logo SENA" 
                                 class="img-fluid sena-loading-logo mx-auto d-block">
                            <p class="mt-3 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Buscando persona...</p>
                        </div>
                    </div>

                    <!-- Información de la persona encontrada -->
                    <div id="persona-info-section" class="d-none">
                        <div class="alert alert-info">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Persona encontrada en el sistema</strong>
                        </div>
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Información de la Persona</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <strong>Nombre Completo:</strong>
                                        <p id="persona-nombre" class="mb-0"></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Número de Documento:</strong>
                                        <p id="persona-documento" class="mb-0"></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Correo Electrónico:</strong>
                                        <p id="persona-email" class="mb-0"></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <strong>Teléfono/Celular:</strong>
                                        <p id="persona-telefono" class="mb-0"></p>
                                    </div>
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ¿Desea agregar esta persona como aspirante al programa?
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mensaje de error -->
                    <div id="error-section" class="alert alert-danger d-none">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span id="error-message"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelar">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary d-none" id="btnAgregarPersonaEncontrada">
                        <i class="fas fa-plus me-1"></i>Agregar al Programa
                    </button>
                    <button type="button" class="btn btn-secondary d-none" id="btnNuevaBusqueda">
                        <i class="fas fa-redo me-1"></i>Nueva Búsqueda
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('resources/css/complementario/ver_aspirantes.css') }}">
<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.9; }
    }
    
    .sena-loading-logo {
        animation: pulse 2s ease-in-out infinite;
        max-width: 120px;
        height: auto;
    }
</style>
@stop

@section('js')
    <script>
        console.log('Módulo de gestión de aspirantes cargado');

        // Variables globales para el progreso
        let progressInterval = null;
        let currentProgressId = null;

        // Variables para el modal de agregar aprendiz
        let personaEncontrada = null;

        // Verificar progreso existente al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($existingProgress) && $existingProgress)
                console.log('Progreso existente encontrado, iniciando monitoreo...');
                currentProgressId = {{ $existingProgress->id }};
                startProgressMonitoring(currentProgressId);
                updateUIForValidationInProgress();
            @endif

            // Configurar formulario de búsqueda
            const formBuscar = document.getElementById('formBuscarAprendiz');
            if (formBuscar) {
                formBuscar.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await buscarPersona();
                });
            }

            // Configurar botón de agregar persona encontrada
            const btnAgregarEncontrada = document.getElementById('btnAgregarPersonaEncontrada');
            if (btnAgregarEncontrada) {
                btnAgregarEncontrada.addEventListener('click', async function() {
                    if (personaEncontrada) {
                        await agregarPersonaEncontrada(personaEncontrada.numero_documento);
                    }
                });
            }

            // Configurar botón de nueva búsqueda
            const btnNuevaBusqueda = document.getElementById('btnNuevaBusqueda');
            if (btnNuevaBusqueda) {
                btnNuevaBusqueda.addEventListener('click', function() {
                    resetearModalBusqueda();
                });
            }

            // Resetear modal cuando se cierra
            $('#modalAgregarAprendiz').on('hidden.bs.modal', function() {
                resetearModalBusqueda();
            });
        });

        // Función para buscar persona
        async function buscarPersona() {
            const numeroDocumento = document.getElementById('numero_documento_buscar').value.trim();
            
            if (!numeroDocumento) {
                mostrarError('Por favor ingrese un número de documento.');
                return;
            }

            // Mostrar loading
            mostrarLoading(true);
            ocultarSecciones();

            try {
                const response = await fetch('{{ route("aspirantes.buscar-persona") }}', {
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
                mostrarLoading(false);

                if (data.success && data.found) {
                    // Persona encontrada
                    personaEncontrada = data.persona;
                    mostrarInformacionPersona(data.persona);
                    
                    // Verificar si ya está inscrita en este programa
                    verificarInscripcionExistente(data.persona.numero_documento);
                } else {
                    // Persona no encontrada - redirigir al formulario
                    window.location.href = `{{ route('aspirantes.create', ['programa' => $programa->id]) }}?numero_documento=${encodeURIComponent(numeroDocumento)}`;
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarLoading(false);
                mostrarError('Error de conexión. Por favor, intente nuevamente.');
            }
        }

        // Función para mostrar información de la persona
        function mostrarInformacionPersona(persona) {
            document.getElementById('persona-nombre').textContent = persona.nombre_completo || 'No disponible';
            document.getElementById('persona-documento').textContent = persona.numero_documento || 'No disponible';
            document.getElementById('persona-email').textContent = persona.email || 'No registrado';
            document.getElementById('persona-telefono').textContent = persona.telefono || 'No registrado';

            document.getElementById('busqueda-section').classList.add('d-none');
            document.getElementById('persona-info-section').classList.remove('d-none');
            document.getElementById('btnAgregarPersonaEncontrada').classList.remove('d-none');
            document.getElementById('btnNuevaBusqueda').classList.remove('d-none');
            document.getElementById('btnCancelar').classList.add('d-none');
        }

        // Función para agregar persona encontrada
        async function agregarPersonaEncontrada(numeroDocumento) {
            const btnAgregar = document.getElementById('btnAgregarPersonaEncontrada');
            const originalText = btnAgregar.innerHTML;
            btnAgregar.disabled = true;
            btnAgregar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Agregando...';

            try {
                const response = await fetch(
                    `{{ route('aspirantes.agregar-existente', ['complementarioId' => $programa->id]) }}`, {
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

                if (data.success) {
                    showAlert('success', data.message);
                    $('#modalAgregarAprendiz').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarError(data.message || 'Error al agregar el aspirante.');
                    btnAgregar.disabled = false;
                    btnAgregar.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarError('Error de conexión. Por favor, intente nuevamente.');
                btnAgregar.disabled = false;
                btnAgregar.innerHTML = originalText;
            }
        }

        // Función para verificar si ya está inscrita
        async function verificarInscripcionExistente(numeroDocumento) {
            // Esta verificación se puede hacer en el servidor cuando se intente agregar
            // Por ahora solo mostramos la información
        }

        // Función para resetear modal
        function resetearModalBusqueda() {
            personaEncontrada = null;
            document.getElementById('numero_documento_buscar').value = '';
            document.getElementById('busqueda-section').classList.remove('d-none');
            document.getElementById('persona-info-section').classList.add('d-none');
            document.getElementById('error-section').classList.add('d-none');
            document.getElementById('btnAgregarPersonaEncontrada').classList.add('d-none');
            document.getElementById('btnNuevaBusqueda').classList.add('d-none');
            document.getElementById('btnCancelar').classList.remove('d-none');
            mostrarLoading(false);
        }

        // Funciones auxiliares
        function mostrarLoading(show) {
            const loading = document.getElementById('loading-busqueda');
            if (loading) {
                if (show) {
                    loading.classList.remove('d-none');
                    document.getElementById('busqueda-section').querySelector('form').style.display = 'none';
                } else {
                    loading.classList.add('d-none');
                    document.getElementById('busqueda-section').querySelector('form').style.display = 'block';
                }
            }
        }

        function mostrarError(mensaje) {
            document.getElementById('error-message').textContent = mensaje;
            document.getElementById('error-section').classList.remove('d-none');
        }

        function ocultarSecciones() {
            document.getElementById('persona-info-section').classList.add('d-none');
            document.getElementById('error-section').classList.add('d-none');
        }

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

            // Deshabilitar botón de agregar aprendiz
            const agregarAprendizButton = document.getElementById('btn-agregar-aprendiz');
            if (agregarAprendizButton) {
                agregarAprendizButton.disabled = true;
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
                const response = await fetch(`{{ route('programas-complementarios.validar-documento', ['programa' => $programa->id]) }}`, {
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
                const response = await fetch(`{{ route('programas-complementarios.validar-sofia', ['programa' => $programa->id]) }}`, {
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

            // Habilitar botón de agregar aprendiz
            const agregarAprendizButton = document.getElementById('btn-agregar-aprendiz');
            if (agregarAprendizButton) {
                agregarAprendizButton.disabled = false;
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
                const destroyUrl = `{{ route('aspirantes.destroy', ['complementarioId' => $programa->id, 'aspiranteId' => 0]) }}`.replace('/0', `/${aspiranteId}`);
                fetch(destroyUrl, {
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
