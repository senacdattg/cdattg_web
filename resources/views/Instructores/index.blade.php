@extends('adminlte::page')

@section('title', 'Lista de Instructores')

@section('css')
    <style>
        .search-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .search-input {
            border-radius: 25px;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .search-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
        }
        .search-btn {
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            background: rgba(255,255,255,0.2);
            color: white;
            transition: all 0.3s ease;
        }
        .search-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        .filter-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #28a745;
        }
        .filter-card h6 {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .filter-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }
        .filter-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .stats-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .table-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: white;
        }
        .table-custom thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .table-custom thead th {
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            padding: 15px 10px;
        }
        .table-custom tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }
        .table-custom tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }
        .table-custom tbody td {
            padding: 12px 10px;
            vertical-align: middle;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .btn-action {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin: 2px;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .btn-create {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 25px;
            color: white;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            color: white;
        }
        .breadcrumb-custom {
            background: transparent;
            padding: 0;
        }
        .breadcrumb-custom .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb-custom .breadcrumb-item.active {
            color: #6c757d;
        }
        .alert-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .pagination-custom {
            justify-content: center;
            margin-top: 30px;
        }
        .pagination-custom .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 2px solid #e9ecef;
            color: #007bff;
        }
        .pagination-custom .page-link:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .pagination-custom .page-item.active .page-link {
            background: #007bff;
            border-color: #007bff;
        }
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 10px;
            }
            .btn-action {
                padding: 4px 8px;
                font-size: 0.7rem;
            }
            .search-card {
                padding: 20px 15px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-primary">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                        Gestión de Instructores
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb breadcrumb-custom float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home.index') }}">
                                <i class="fas fa-home mr-1"></i>Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-chalkboard-teacher mr-1"></i>Instructores
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Alertas -->
            @if (session('success'))
                <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Barra de Búsqueda -->
            <div class="search-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-3">
                            <i class="fas fa-search mr-2"></i>
                            Buscar Instructores
                        </h4>
                        <form method="GET" action="{{ route('instructor.index') }}" class="d-flex">
                            <input type="text" 
                                   name="search" 
                                   class="form-control search-input flex-grow-1 mr-3" 
                                   placeholder="Buscar por nombre, apellido, documento o email..."
                                   value="{{ request()->input('search') }}">
                            <button type="submit" class="btn search-btn">
                                <i class="fas fa-search mr-1"></i>
                                Buscar
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4 text-right">
                        @can('CREAR INSTRUCTOR')
                            <a href="{{ route('instructor.create') }}" class="btn btn-create">
                                <i class="fas fa-plus mr-2"></i>
                                Nuevo Instructor
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Filtros Avanzados -->
            <div class="row">
                <div class="col-md-3">
                    <div class="filter-card">
                        <h6>
                            <i class="fas fa-filter mr-2"></i>
                            Filtros Rápidos
                        </h6>
                        <div class="form-group">
                            <label for="status_filter">Estado</label>
                            <select class="form-control filter-select" id="status_filter">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="regional_filter">Regional</label>
                            <select class="form-control filter-select" id="regional_filter">
                                <option value="">Todas las regionales</option>
                                @foreach($regionales ?? [] as $regional)
                                    <option value="{{ $regional->id }}" {{ request('regional') == $regional->id ? 'selected' : '' }}>
                                        {{ $regional->regional }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fichas_filter">Fichas</label>
                            <select class="form-control filter-select" id="fichas_filter">
                                <option value="">Todos</option>
                                <option value="con_fichas" {{ request('fichas') == 'con_fichas' ? 'selected' : '' }}>Con fichas</option>
                                <option value="sin_fichas" {{ request('fichas') == 'sin_fichas' ? 'selected' : '' }}>Sin fichas</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $instructores->total() }}</div>
                                <div class="stats-label">
                                    <i class="fas fa-users mr-1"></i>
                                    Total Instructores
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $instructores->where('persona.user.status', 1)->count() }}</div>
                                <div class="stats-label">
                                    <i class="fas fa-user-check mr-1"></i>
                                    Activos
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $instructores->where('persona.user.status', 0)->count() }}</div>
                                <div class="stats-label">
                                    <i class="fas fa-user-times mr-1"></i>
                                    Inactivos
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $instructores->whereDoesntHave('fichas')->count() }}</div>
                                <div class="stats-label">
                                    <i class="fas fa-user-clock mr-1"></i>
                                    Disponibles
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Instructores -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title text-primary mb-0">
                                <i class="fas fa-list mr-2"></i>
                                Lista de Instructores
                                @if(request()->has('search'))
                                    <small class="text-muted">- Resultados para: "{{ request('search') }}"</small>
                                @endif
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @if($instructores->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-custom mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">#</th>
                                                <th style="width: 25%">
                                                    <i class="fas fa-user mr-1"></i>
                                                    Nombre Completo
                                                </th>
                                                <th style="width: 15%">
                                                    <i class="fas fa-id-card mr-1"></i>
                                                    Documento
                                                </th>
                                                <th style="width: 20%">
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    Email
                                                </th>
                                                <th style="width: 15%">
                                                    <i class="fas fa-building mr-1"></i>
                                                    Regional
                                                </th>
                                                <th style="width: 10%">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Estado
                                                </th>
                                                <th style="width: 10%">
                                                    <i class="fas fa-cogs mr-1"></i>
                                                    Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($instructores as $index => $instructor)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $instructores->firstItem() + $index }}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ asset('dist/img/LogoSena.jpeg') }}" 
                                                                 alt="Avatar" 
                                                                 class="rounded-circle mr-3" 
                                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                                            <div>
                                                                <strong>{{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}</strong>
                                                                @if($instructor->persona->segundo_nombre || $instructor->persona->segundo_apellido)
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        {{ $instructor->persona->segundo_nombre }} {{ $instructor->persona->segundo_apellido }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $instructor->persona->numero_documento }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $instructor->persona->tipoDocumento->name ?? 'N/A' }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="mailto:{{ $instructor->persona->email }}" class="text-primary">
                                                            {{ $instructor->persona->email }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ $instructor->regional->regional ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge {{ $instructor->persona->user->status === 1 ? 'status-active' : 'status-inactive' }}">
                                                            {{ $instructor->persona->user->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @can('VER INSTRUCTOR')
                                                                <a href="{{ route('instructor.show', $instructor->id) }}" 
                                                                   class="btn btn-info btn-action" 
                                                                   data-toggle="tooltip" 
                                                                   title="Ver perfil">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endcan

                                                            @can('EDITAR INSTRUCTOR')
                                                                <a href="{{ route('instructor.edit', $instructor->id) }}" 
                                                                   class="btn btn-warning btn-action" 
                                                                   data-toggle="tooltip" 
                                                                   title="Editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            @endcan

                                                            @can('EDITAR INSTRUCTOR')
                                                                <form action="{{ route('persona.cambiarEstadoUser', $instructor->persona->user->id) }}" 
                                                                      method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" 
                                                                            class="btn btn-success btn-action" 
                                                                            data-toggle="tooltip" 
                                                                            title="{{ $instructor->persona->user->status === 1 ? 'Desactivar' : 'Activar' }}"
                                                                            onclick="return confirm('¿Cambiar estado del usuario?')">
                                                                        <i class="fas fa-sync"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan

                                                            @can('ELIMINAR INSTRUCTOR')
                                                                <form action="{{ route('instructor.destroy', $instructor->id) }}" 
                                                                      method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" 
                                                                            class="btn btn-danger btn-action" 
                                                                            data-toggle="tooltip" 
                                                                            title="Eliminar"
                                                                            onclick="return confirm('¿Eliminar instructor? Esta acción no se puede deshacer.')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <h4>No se encontraron instructores</h4>
                                    <p class="text-muted">
                                        @if(request()->has('search'))
                                            No hay instructores que coincidan con la búsqueda "{{ request('search') }}".
                                        @else
                                            No hay instructores registrados en el sistema.
                                        @endif
                                    </p>
                                    @can('CREAR INSTRUCTOR')
                                        <a href="{{ route('instructor.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus mr-2"></i>
                                            Crear Primer Instructor
                                        </a>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            @if($instructores->hasPages())
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="Paginación de instructores">
                            {{ $instructores->appends(request()->query())->links('pagination::bootstrap-4', ['class' => 'pagination-custom']) }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Aplicar filtros automáticamente
            $('.filter-select').on('change', function() {
                const url = new URL(window.location);
                const filterName = $(this).attr('id').replace('_filter', '');
                const filterValue = $(this).val();
                
                if (filterValue) {
                    url.searchParams.set(filterName, filterValue);
                } else {
                    url.searchParams.delete(filterName);
                }
                
                window.location.href = url.toString();
            });

            // Limpiar filtros
            $('#clear_filters').on('click', function() {
                window.location.href = '{{ route("instructor.index") }}';
            });

            // Tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Confirmación para acciones críticas
            $('form').on('submit', function(e) {
                const button = $(this).find('button[type="submit"]');
                const title = button.attr('title');
                
                if (title && (title.includes('Eliminar') || title.includes('Desactivar') || title.includes('Activar'))) {
                    e.preventDefault();
                    const form = this;
                    
                    Swal.fire({
                        title: '¿Confirmar Acción?',
                        text: 'Esta acción puede tener consecuencias importantes. ¿Está seguro?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });

            // Animación de entrada para las tarjetas
            $('.stats-card, .filter-card').each(function(index) {
                $(this).css('opacity', '0').delay(index * 100).animate({
                    opacity: 1
                }, 500);
            });

            // Búsqueda en tiempo real (opcional)
            let searchTimeout;
            $('input[name="search"]').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    // Aquí se podría implementar búsqueda AJAX si se desea
                }, 500);
            });
        });
    </script>
@endsection