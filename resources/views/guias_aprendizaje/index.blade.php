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

            <!-- Filtros Avanzados -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card filter-card">
                        <div class="card-header">
                            <h5 class="card-title m-0">
                                <i class="fas fa-filter mr-2"></i>Filtros Avanzados
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('guias-aprendizaje.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Buscar por código o nombre</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Código o nombre..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Estado</label>
                                    <select name="status" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha desde</label>
                                    <input type="date" name="fecha_desde" class="form-control" 
                                           value="{{ request('fecha_desde') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha hasta</label>
                                    <input type="date" name="fecha_hasta" class="form-control" 
                                           value="{{ request('fecha_hasta') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Ordenar por</label>
                                    <select name="ordenar" class="form-control">
                                        <option value="created_at_desc" {{ request('ordenar') == 'created_at_desc' ? 'selected' : '' }}>Más recientes</option>
                                        <option value="nombre_asc" {{ request('ordenar') == 'nombre_asc' ? 'selected' : '' }}>Nombre A-Z</option>
                                        <option value="codigo_asc" {{ request('ordenar') == 'codigo_asc' ? 'selected' : '' }}>Código A-Z</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
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
            $('select[name="status"], select[name="ordenar"]').on('change', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endsection
