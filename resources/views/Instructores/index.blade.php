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

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number">{{ $estadisticas['total'] }}</div>
                        <div class="stats-label">Total Instructores</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="stats-number">{{ $estadisticas['activos'] }}</div>
                        <div class="stats-label">Activos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="stats-number">{{ $estadisticas['inactivos'] }}</div>
                        <div class="stats-label">Inactivos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                        <div class="stats-number">{{ $estadisticas['con_fichas'] }}</div>
                        <div class="stats-label">Con Fichas</div>
                    </div>
                </div>
            </div>

            <!-- Filtros Avanzados -->
            <div class="filter-card">
                <h6><i class="fas fa-filter mr-2"></i>Filtros Avanzados</h6>
                <form id="searchForm">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Estado</label>
                            <select name="estado" class="form-control filter-select" id="filtroEstado">
                                <option value="todos" {{ $filtroEstado === 'todos' ? 'selected' : '' }}>Todos</option>
                                <option value="activos" {{ $filtroEstado === 'activos' ? 'selected' : '' }}>Activos</option>
                                <option value="inactivos" {{ $filtroEstado === 'inactivos' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Especialidad</label>
                            <select name="especialidad" class="form-control filter-select" id="filtroEspecialidad">
                                <option value="">Todas</option>
                                @foreach($especialidades as $especialidad)
                                    <option value="{{ $especialidad->nombre }}" {{ $filtroEspecialidad === $especialidad->nombre ? 'selected' : '' }}>
                                        {{ $especialidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Regional</label>
                            <select name="regional" class="form-control filter-select" id="filtroRegional">
                                <option value="">Todas</option>
                                @foreach($regionales as $regional)
                                    <option value="{{ $regional->id }}" {{ $filtroRegional == $regional->id ? 'selected' : '' }}>
                                        {{ $regional->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-success btn-block" id="btnFiltrar">
                                <i class="fas fa-search mr-1"></i>Buscar
                            </button>
                        </div>
                    </div>
                </form>
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
                        <div class="card-body">
                            <div id="instructores-container">
                                @include('Instructores.partials.instructores-table')
                            </div>
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
            let searchTimeout;
            
            // Búsqueda en tiempo real
            $('#searchInput').on('keyup', function() {
                const searchTerm = $(this).val();
                
                // Debounce search
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch();
                }, 300);
            });

            // Filtros automáticos
            $('#filtroEstado, #filtroEspecialidad, #filtroRegional').on('change', function() {
                performSearch();
            });

            // Botón de búsqueda
            $('#btnFiltrar').on('click', function() {
                performSearch();
            });

            // Función principal de búsqueda AJAX
            function performSearch() {
                const searchTerm = $('#searchInput').val();
                const estado = $('#filtroEstado').val();
                const especialidad = $('#filtroEspecialidad').val();
                const regional = $('#filtroRegional').val();

                // Mostrar loading
                $('#instructores-container').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Buscando instructores...</p>
                    </div>
                `);

                $.ajax({
                    url: '{{ route("instructor.search") }}',
                    method: 'GET',
                    data: {
                        search: searchTerm,
                        estado: estado,
                        especialidad: especialidad,
                        regional: regional,
                        page: 1
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#instructores-container').html(response.html);
                            
                            // Actualizar URL sin recargar la página
                            updateURL(searchTerm, estado, especialidad, regional);
                            
                            // Reinicializar tooltips
                            $('[data-toggle="tooltip"]').tooltip();
                        } else {
                            showError('Error en la búsqueda: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en búsqueda:', error);
                        showError('Error en la búsqueda. Por favor, inténtelo de nuevo.');
                    }
                });
            }

            // Manejar paginación AJAX
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const page = new URL(url).searchParams.get('page');
                
                if (page) {
                    performPagination(page);
                }
            });

            // Función de paginación AJAX
            function performPagination(page) {
                const searchTerm = $('#searchInput').val();
                const estado = $('#filtroEstado').val();
                const especialidad = $('#filtroEspecialidad').val();
                const regional = $('#filtroRegional').val();

                $('#instructores-container').html(`
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                `);

                $.ajax({
                    url: '{{ route("instructor.search") }}',
                    method: 'GET',
                    data: {
                        search: searchTerm,
                        estado: estado,
                        especialidad: especialidad,
                        regional: regional,
                        page: page
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#instructores-container').html(response.html);
                            $('[data-toggle="tooltip"]').tooltip();
                            
                            // Scroll to top
                            $('html, body').animate({ scrollTop: $('#instructores-container').offset().top - 100 }, 300);
                        } else {
                            showError('Error en la paginación: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en paginación:', error);
                        showError('Error en la paginación. Por favor, inténtelo de nuevo.');
                    }
                });
            }

            // Actualizar URL sin recargar página
            function updateURL(search, estado, especialidad, regional) {
                const url = new URL(window.location);
                
                if (search) url.searchParams.set('search', search);
                else url.searchParams.delete('search');
                
                if (estado && estado !== 'todos') url.searchParams.set('estado', estado);
                else url.searchParams.delete('estado');
                
                if (especialidad) url.searchParams.set('especialidad', especialidad);
                else url.searchParams.delete('especialidad');
                
                if (regional) url.searchParams.set('regional', regional);
                else url.searchParams.delete('regional');
                
                // Actualizar URL sin recargar
                window.history.pushState({}, '', url.toString());
            }

            // Mostrar error
            function showError(message) {
                $('#instructores-container').html(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
            }

            // Animación de entrada para las tarjetas
            $('.stats-card, .filter-card').each(function(index) {
                $(this).css('opacity', '0').delay(index * 100).animate({
                    opacity: 1
                }, 500);
            });

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Tooltip initialization
            $('[data-toggle="tooltip"]').tooltip();

            // Limpiar filtros
            $('#btnLimpiar').on('click', function() {
                $('#searchInput').val('');
                $('#filtroEstado').val('todos');
                $('#filtroEspecialidad').val('');
                $('#filtroRegional').val('');
                performSearch();
            });

            // Confirmación para acciones críticas
            $(document).on('submit', 'form', function(e) {
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
        });
    </script>
@endsection