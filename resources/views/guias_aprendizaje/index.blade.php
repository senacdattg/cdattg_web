@extends('adminlte::page')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .filter-card {
            border-left: 4px solid #667eea;
        }
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
        .badge-status {
            font-size: 0.8em;
            padding: 0.4em 0.8em;
        }
        .search-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .filter-section {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .filter-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .search-results {
            min-height: 200px;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        .search-stats {
            background: #e9ecef;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-book-open text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Guías de Aprendizaje</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión integral de guías de aprendizaje</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-book-open"></i> Guías de Aprendizaje
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $guiasAprendizaje->total() }}</h4>
                                    <p class="mb-0">Total Guías</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-book fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $guiasAprendizaje->where('status', 1)->count() }}</h4>
                                    <p class="mb-0">Activas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $guiasAprendizaje->where('status', 0)->count() }}</h4>
                                    <p class="mb-0">Inactivas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-pause-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $guiasAprendizaje->where('created_at', '>=', now()->subDays(30))->count() }}</h4>
                                    <p class="mb-0">Últimos 30 días</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Búsqueda Avanzada -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="search-container">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-search"></i>
                                    Búsqueda Avanzada
                                </h5>
                            </div>
                        </div>
                        
                        <form id="searchForm" method="GET" action="{{ route('guias-aprendizaje.index') }}">
                            <!-- Búsqueda General -->
                            <div class="filter-section">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="search">Búsqueda General</label>
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="search" 
                                                       name="search" 
                                                       value="{{ request('search') }}"
                                                       placeholder="Buscar por código, nombre o descripción...">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="">Todos los estados</option>
                                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtros Específicos -->
                            <div class="filter-section">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="codigo">Código</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="codigo" 
                                                   name="codigo" 
                                                   value="{{ request('codigo') }}"
                                                   placeholder="Filtrar por código...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nombre">Nombre</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="nombre" 
                                                   name="nombre" 
                                                   value="{{ request('nombre') }}"
                                                   placeholder="Filtrar por nombre...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nivel_dificultad">Nivel de Dificultad</label>
                                            <select class="form-control" id="nivel_dificultad" name="nivel_dificultad">
                                                <option value="">Todos los niveles</option>
                                                <option value="BASICO" {{ request('nivel_dificultad') == 'BASICO' ? 'selected' : '' }}>Básico</option>
                                                <option value="INTERMEDIO" {{ request('nivel_dificultad') == 'INTERMEDIO' ? 'selected' : '' }}>Intermedio</option>
                                                <option value="AVANZADO" {{ request('nivel_dificultad') == 'AVANZADO' ? 'selected' : '' }}>Avanzado</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtros por Relaciones -->
                            <div class="filter-section">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="competencia_id">Competencia</label>
                                            <select class="form-control select2" id="competencia_id" name="competencia_id">
                                                <option value="">Todas las competencias</option>
                                                @foreach($competencias as $competencia)
                                                    <option value="{{ $competencia->id }}" {{ request('competencia_id') == $competencia->id ? 'selected' : '' }}>
                                                        {{ $competencia->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="resultado_id">Resultado de Aprendizaje</label>
                                            <select class="form-control select2" id="resultado_id" name="resultado_id">
                                                <option value="">Todos los resultados</option>
                                                @foreach($resultadosAprendizaje as $resultado)
                                                    <option value="{{ $resultado->id }}" {{ request('resultado_id') == $resultado->id ? 'selected' : '' }}>
                                                        {{ $resultado->codigo }} - {{ $resultado->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user_create_id">Creado por</label>
                                            <select class="form-control select2" id="user_create_id" name="user_create_id">
                                                <option value="">Todos los usuarios</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}" {{ request('user_create_id') == $usuario->id ? 'selected' : '' }}>
                                                        {{ $usuario->persona->primer_nombre ?? $usuario->name }} {{ $usuario->persona->primer_apellido ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtros por Fecha -->
                            <div class="filter-section">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fecha_desde">Fecha Desde</label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="fecha_desde" 
                                                   name="fecha_desde" 
                                                   value="{{ request('fecha_desde') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fecha_hasta">Fecha Hasta</label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="fecha_hasta" 
                                                   name="fecha_hasta" 
                                                   value="{{ request('fecha_hasta') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sort_by">Ordenar por</label>
                                            <select class="form-control" id="sort_by" name="sort_by">
                                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Fecha de Creación</option>
                                                <option value="nombre" {{ request('sort_by') == 'nombre' ? 'selected' : '' }}>Nombre</option>
                                                <option value="codigo" {{ request('sort_by') == 'codigo' ? 'selected' : '' }}>Código</option>
                                                <option value="nivel_dificultad" {{ request('sort_by') == 'nivel_dificultad' ? 'selected' : '' }}>Nivel de Dificultad</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                        <i class="fas fa-eraser"></i> Limpiar Filtros
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Botón Crear -->
            @can('CREAR GUIA APRENDIZAJE')
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="{{ route('guias-aprendizaje.create') }}" class="btn btn-success">
                            <i class="fas fa-plus mr-2"></i>Nueva Guía de Aprendizaje
                        </a>
                    </div>
                </div>
            @endcan

            <!-- Tabla de Guías -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list mr-2"></i>Lista de Guías de Aprendizaje
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="px-4 py-3" style="width: 5%">#</th>
                                            <th class="px-4 py-3" style="width: 15%">Código</th>
                                            <th class="px-4 py-3" style="width: 25%">Nombre</th>
                                            <th class="px-4 py-3" style="width: 15%">Estado</th>
                                            <th class="px-4 py-3" style="width: 15%">Resultados</th>
                                            <th class="px-4 py-3" style="width: 15%">Actividades</th>
                                            <th class="px-4 py-3 text-center" style="width: 10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($guiasAprendizaje as $guia)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4">
                                                    <span class="font-weight-bold text-primary">{{ $guia->codigo }}</span>
                                                </td>
                                                <td class="px-4">
                                                    <div>
                                                        <div class="font-weight-medium">{{ $guia->nombre }}</div>
                                                        <small class="text-muted">
                                                            Creada: {{ $guia->created_at->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="px-4">
                                                    <span class="badge badge-status {{ $guia->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                                        {{ $guia->status == 1 ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </td>
                                                <td class="px-4">
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-target mr-1"></i>
                                                        {{ $guia->resultadosAprendizaje->count() }}
                                                    </span>
                                                </td>
                                                <td class="px-4">
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-tasks mr-1"></i>
                                                        {{ $guia->actividades->count() }}
                                                    </span>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('VER GUIA APRENDIZAJE')
                                                            <a href="{{ route('guias-aprendizaje.show', $guia) }}" 
                                                               class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR GUIA APRENDIZAJE')
                                                            <a href="{{ route('guias-aprendizaje.edit', $guia) }}" 
                                                               class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR GUIA APRENDIZAJE')
                                                            <form action="{{ route('guias-aprendizaje.cambiarEstado', $guia) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-light btn-sm" 
                                                                        data-toggle="tooltip" title="Cambiar estado">
                                                                    <i class="fas fa-sync text-success"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                        @can('ELIMINAR GUIA APRENDIZAJE')
                                                            <form action="{{ route('guias-aprendizaje.destroy', $guia) }}" 
                                                                  method="POST" class="d-inline formulario-eliminar">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light btn-sm" 
                                                                        data-toggle="tooltip" title="Eliminar">
                                                                    <i class="fas fa-trash text-danger"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-book-open fa-3x mb-3"></i>
                                                        <p class="h5">No hay guías de aprendizaje registradas</p>
                                                        <p>Crea tu primera guía de aprendizaje para comenzar</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if($guiasAprendizaje->hasPages())
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted">
                                        Mostrando {{ $guiasAprendizaje->firstItem() }} a {{ $guiasAprendizaje->lastItem() }} 
                                        de {{ $guiasAprendizaje->total() }} resultados
                                    </div>
                                    <div>
                                        {{ $guiasAprendizaje->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Confirmación de eliminación
            $('.formulario-eliminar').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Auto-submit del formulario de filtros al cambiar select
            $('select[name="status"], select[name="sort_by"]').on('change', function() {
                $(this).closest('form').submit();
            });

            // Búsqueda en tiempo real con AJAX
            let searchTimeout;
            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val();
                
                if (query.length >= 2 || query.length === 0) {
                    searchTimeout = setTimeout(function() {
                        performAjaxSearch();
                    }, 500);
                }
            });

            // Función para realizar búsqueda AJAX
            function performAjaxSearch() {
                const formData = $('#searchForm').serialize();
                
                $.ajax({
                    url: '{{ route("guias-aprendizaje.search") }}',
                    method: 'GET',
                    data: formData,
                    beforeSend: function() {
                        $('.loading-spinner').show();
                        $('.search-results').hide();
                    },
                    success: function(response) {
                        if (response.success) {
                            updateSearchResults(response);
                        } else {
                            showErrorMessage(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error en búsqueda AJAX:', xhr);
                        showErrorMessage('Error al realizar la búsqueda');
                    },
                    complete: function() {
                        $('.loading-spinner').hide();
                    }
                });
            }

            // Actualizar resultados de búsqueda
            function updateSearchResults(response) {
                const resultsContainer = $('.search-results');
                
                if (response.data.length === 0) {
                    resultsContainer.html(`
                        <div class="no-results">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <h5>No se encontraron resultados</h5>
                            <p>Intenta con otros términos de búsqueda</p>
                        </div>
                    `);
                } else {
                    let html = '<div class="table-responsive"><table class="table table-striped">';
                    html += '<thead><tr><th>Código</th><th>Nombre</th><th>Estado</th><th>Resultados</th><th>Actividades</th><th>Acciones</th></tr></thead>';
                    html += '<tbody>';
                    
                    response.data.forEach(function(guia) {
                        html += `
                            <tr>
                                <td><span class="badge badge-primary">${guia.codigo}</span></td>
                                <td>${guia.nombre}</td>
                                <td>
                                    <span class="badge badge-status ${guia.status == 1 ? 'badge-success' : 'badge-danger'}">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        ${guia.status == 1 ? 'Activo' : 'Inactivo'}
                                    </span>
                                </td>
                                <td><span class="badge badge-info">${guia.resultados_aprendizaje ? guia.resultados_aprendizaje.length : 0}</span></td>
                                <td><span class="badge badge-warning">${guia.actividades ? guia.actividades.length : 0}</span></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="/guias-aprendizaje/${guia.id}" class="btn btn-light btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye text-info"></i>
                                        </a>
                                        <a href="/guias-aprendizaje/${guia.id}/edit" class="btn btn-light btn-sm" title="Editar">
                                            <i class="fas fa-pencil-alt text-warning"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += '</tbody></table></div>';
                    
                    // Agregar paginación si existe
                    if (response.links) {
                        html += '<div class="d-flex justify-content-center mt-3">' + response.links + '</div>';
                    }
                    
                    resultsContainer.html(html);
                }
                
                resultsContainer.show();
            }

            // Mostrar mensaje de error
            function showErrorMessage(message) {
                $('.search-results').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        ${message}
                    </div>
                `).show();
            }

            // Función para limpiar filtros
            window.clearFilters = function() {
                $('#searchForm')[0].reset();
                window.location.href = '{{ route("guias-aprendizaje.index") }}';
            };

            // Inicializar Select2
            $('.select2').select2({
                placeholder: 'Seleccionar...',
                allowClear: true
            });
        });
    </script>
@endsection
