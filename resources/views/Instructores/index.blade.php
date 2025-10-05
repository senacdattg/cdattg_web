@extends('adminlte::page')

@section('css')
    @vite(['parametros_css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-chalkboard-teacher text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Instructores</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de instructores del sistema</p>
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
                                <i class="fas fa-chalkboard-teacher"></i> Instructores
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
            <div class="row">
                <div class="col-12">
                    @can('CREAR INSTRUCTOR')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                    <i class="fas fa-plus-circle mr-2"></i> 
                                    <a href="{{ route('instructor.create') }}" class="text-primary text-decoration-none">Nuevo Instructor</a>
                                </h5>
                            </div>
                        </div>
                    @endcan

                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Instructores
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['total'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Activos
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['activos'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-check fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Inactivos
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['inactivos'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-times fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Con Fichas
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['con_fichas'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros Avanzados -->
                    <div class="card shadow-sm mb-4 no-hover">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                                <i class="fas fa-filter mr-2"></i> Filtros de Búsqueda
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="searchForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Búsqueda</label>
                                        <input type="text" name="search" id="searchInput" class="form-control" 
                                               placeholder="Nombre, documento o email..." 
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Estado</label>
                                        <select name="estado" class="form-control" id="filtroEstado">
                                            <option value="todos" {{ $filtroEstado === 'todos' ? 'selected' : '' }}>Todos</option>
                                            <option value="activos" {{ $filtroEstado === 'activos' ? 'selected' : '' }}>Activos</option>
                                            <option value="inactivos" {{ $filtroEstado === 'inactivos' ? 'selected' : '' }}>Inactivos</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Especialidad</label>
                                        <select name="especialidad" class="form-control" id="filtroEspecialidad">
                                            <option value="">Todas</option>
                                            @foreach($especialidades as $especialidad)
                                                <option value="{{ $especialidad->nombre }}" {{ $filtroEspecialidad === $especialidad->nombre ? 'selected' : '' }}>
                                                    {{ $especialidad->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Regional</label>
                                        <select name="regional" class="form-control" id="filtroRegional">
                                            <option value="">Todas</option>
                                            @foreach($regionales as $regional)
                                                <option value="{{ $regional->id }}" {{ $filtroRegional == $regional->id ? 'selected' : '' }}>
                                                    {{ $regional->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex">
                                            <button type="button" class="btn btn-primary btn-sm mr-2" id="btnFiltrar">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLimpiar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">Lista de Instructores</h6>
                            <div class="input-group w-25">
                                <form action="{{ route('instructor.index') }}" method="GET" class="input-group">
                                    <input type="text" name="search" id="searchParameter"
                                        class="form-control form-control-sm" placeholder="Buscar instructor..."
                                        value="{{ request('search') }}" autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="instructores-container">
                                @include('Instructores.partials.instructores-table')
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="float-right">
                                {{ $instructores->links() }}
                            </div>
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
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

            // Tooltip initialization
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection