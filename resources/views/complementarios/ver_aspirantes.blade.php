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
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar por nombre o número de identidad">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>{{ $programa->nombre }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>Todos los años</option>
                        <option>2025</option>
                        <option>2024</option>
                    </select>
                </div>
            </form>
            <div class="mt-3">
                <button class="btn btn-outline-secondary btn-sm me-2">Todos</button>
                <button class="btn btn-outline-warning btn-sm me-2">En Proceso</button>
                <button class="btn btn-outline-success btn-sm me-2">Aceptados</button>
                <button class="btn btn-outline-danger btn-sm me-2">Rechazados</button>
                <button class="btn btn-outline-primary btn-sm" id="btn-validar-sofia">
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

        // Botón de validación SenaSofiaPlus
        document.getElementById('btn-validar-sofia').addEventListener('click', async function() {
            const button = this;
            const originalText = button.innerHTML;

            // Deshabilitar botón y mostrar loading
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Validando...';

            try {
                // Obtener el ID del programa desde la URL
                const urlParts = window.location.pathname.split('/');
                const programaId = urlParts[urlParts.length - 1];

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
                    // Mostrar mensaje de éxito
                    showAlert('success', 'Validación completada exitosamente');

                    // Recargar la página para mostrar los cambios
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    // Mostrar mensaje de error
                    showAlert('error', data.message || 'Error durante la validación');
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