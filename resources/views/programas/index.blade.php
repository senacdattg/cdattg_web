@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-graduation-cap text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Programas de Formación</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de programas de formación del SENA</p>
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
                                <i class="fas fa-graduation-cap"></i> Programas de Formación
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
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            <div class="row">
                <div class="col-12">
                    @can('programa.create')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear Programa de Formación
                                </h5>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                                    data-target="#createProgramaForm" aria-expanded="true">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>

                            <div class="collapse show" id="createProgramaForm">
                                <div class="card-body">
                                    @include('programas.create')
                                </div>
                            </div>
                        </div>
                    @endcan

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">Lista de Programas de Formación</h6>
                            <div class="d-flex align-items-center">
                                <!-- Filtros avanzados -->
                                <div class="mr-3">
                                    <select id="filterRedConocimiento" class="form-control form-control-sm" style="width: 150px;">
                                        <option value="">Todas las redes</option>
                                        @foreach(\App\Models\RedConocimiento::all() as $red)
                                            <option value="{{ $red->id }}">{{ $red->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mr-3">
                                    <select id="filterNivelFormacion" class="form-control form-control-sm" style="width: 120px;">
                                        <option value="">Todos los niveles</option>
                                        @foreach(\App\Models\Parametro::whereHas('temas', function($query) { $query->where('temas.id', 6); })->get() as $nivel)
                                            <option value="{{ $nivel->id }}">{{ $nivel->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mr-3">
                                    <select id="filterStatus" class="form-control form-control-sm" style="width: 100px;">
                                        <option value="">Todos</option>
                                        <option value="1">Activos</option>
                                        <option value="0">Inactivos</option>
                                    </select>
                                </div>
                                <!-- Barra de búsqueda -->
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" id="searchPrograma" class="form-control form-control-sm" 
                                           placeholder="Buscar por código, nombre..." autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="button" id="btnSearch">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <button class="btn btn-secondary btn-sm" type="button" id="btnClearFilters">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="px-4 py-3" style="width: 5%">#</th>
                                            <th class="px-4 py-3" style="width: 10%">Código</th>
                                            <th class="px-4 py-3" style="width: 30%">Nombre</th>
                                            <th class="px-4 py-3" style="width: 20%">Red de Conocimiento</th>
                                            <th class="px-4 py-3" style="width: 15%">Nivel</th>
                                            <th class="px-4 py-3" style="width: 10%">Estado</th>
                                            <th class="px-4 py-3 text-center" style="width: 10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="programasTableBody">
                                        @forelse ($programas as $programa)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4">
                                                    <span class="badge badge-secondary">{{ $programa->codigo }}</span>
                                                </td>
                                                <td class="px-4 font-weight-medium">{{ $programa->nombre }}</td>
                                                <td class="px-4">
                                                    @if ($programa->redConocimiento)
                                                        <span class="text-primary">
                                                            <i class="fas fa-network-wired mr-1"></i>
                                                            {{ Str::limit($programa->redConocimiento->nombre, 30) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    @if ($programa->nivelFormacion)
                                                        <span class="text-success">
                                                            <i class="fas fa-layer-group mr-1"></i>
                                                            {{ $programa->nivelFormacion->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $programa->status ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $programa->status ? 'Activo' : 'Inactivo' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('programa.edit')
                                                            <form action="{{ route('programa.cambiarEstado', ['programa' => $programa->id]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-light btn-sm" data-toggle="tooltip" title="Cambiar estado">
                                                                    <i class="fas fa-sync text-success"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                        @can('programa.show')
                                                            <a href="{{ route('programa.show', ['programa' => $programa->id]) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('programa.edit')
                                                            <a href="{{ route('programa.edit', ['programa' => $programa->id]) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('programa.delete')
                                                            <form action="{{ route('programa.destroy', ['programa' => $programa->id]) }}" method="POST" class="d-inline formulario-eliminar">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light btn-sm" data-toggle="tooltip" title="Eliminar">
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
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data" style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay programas de formación registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="float-right">
                                {{ $programas->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('components.confirm-delete-modal')
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let searchTimeout;
        
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Confirmar eliminación con diseño unificado
        $('.formulario-eliminar').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const nombre = form.closest('tr').find('td:nth-child(3)').text().trim();
            
            confirmDelete(nombre, form.attr('action'), form[0]);
        });

        // Búsqueda en tiempo real con debounce
        $('#searchPrograma').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performAjaxSearch();
            }, 500);
        });

        // Botón de búsqueda
        $('#btnSearch').on('click', function() {
            performAjaxSearch();
        });

        // Limpiar filtros
        $('#btnClearFilters').on('click', function() {
            $('#searchPrograma').val('');
            $('#filterRedConocimiento').val('');
            $('#filterNivelFormacion').val('');
            $('#filterStatus').val('');
            performAjaxSearch();
        });

        // Cambios en filtros
        $('#filterRedConocimiento, #filterNivelFormacion, #filterStatus').on('change', function() {
            performAjaxSearch();
        });

        // Función para realizar búsqueda AJAX
        function performAjaxSearch() {
            const searchTerm = $('#searchPrograma').val();
            const redConocimientoId = $('#filterRedConocimiento').val();
            const nivelFormacionId = $('#filterNivelFormacion').val();
            const status = $('#filterStatus').val();

            showLoadingState();

            $.ajax({
                url: '{{ route("programa.search") }}',
                method: 'GET',
                data: {
                    search: searchTerm,
                    red_conocimiento_id: redConocimientoId,
                    nivel_formacion_id: nivelFormacionId,
                    status: status,
                    per_page: 6
                },
                success: function(response) {
                    updateTableContent(response.data.programas);
                    updatePagination(response.data.pagination);
                },
                error: function(xhr) {
                    console.error('Error en búsqueda:', xhr);
                    showError('Error al realizar la búsqueda');
                }
            });
        }

        // Mostrar estado de carga
        function showLoadingState() {
            $('#programasTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Buscando...</span>
                        </div>
                        <p class="mt-2 text-muted">Buscando programas...</p>
                    </td>
                </tr>
            `);
        }

        // Mostrar error
        function showError(message) {
            $('#programasTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p class="mb-0">${message}</p>
                        </div>
                    </td>
                </tr>
            `);
        }

        // Actualizar contenido de la tabla
        function updateTableContent(programas) {
            if (programas.length === 0) {
                $('#programasTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <p class="mb-0">No se encontraron programas que coincidan con los criterios de búsqueda</p>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            let tableRows = '';
            programas.forEach(function(programa, index) {
                const statusClass = programa.status ? 'bg-success-light text-success' : 'bg-danger-light text-danger';
                const statusText = programa.status ? 'Activo' : 'Inactivo';
                
                tableRows += `
                    <tr>
                        <td class="px-4">${index + 1}</td>
                        <td class="px-4">
                            <span class="badge badge-secondary">${programa.codigo}</span>
                        </td>
                        <td class="px-4 font-weight-medium">${programa.nombre}</td>
                        <td class="px-4">
                            ${programa.red_conocimiento ? 
                                `<span class="text-primary">
                                    <i class="fas fa-network-wired mr-1"></i>
                                    ${programa.red_conocimiento.nombre.length > 30 ? 
                                        programa.red_conocimiento.nombre.substring(0, 30) + '...' : 
                                        programa.red_conocimiento.nombre
                                    }
                                </span>` : 
                                '<span class="text-muted">Sin asignar</span>'
                            }
                        </td>
                        <td class="px-4">
                            ${programa.nivel_formacion ? 
                                `<span class="text-success">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    ${programa.nivel_formacion.name}
                                </span>` : 
                                '<span class="text-muted">Sin asignar</span>'
                            }
                        </td>
                        <td class="px-4">
                            <div class="d-inline-block px-3 py-1 rounded-pill ${statusClass}">
                                <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                ${statusText}
                            </div>
                        </td>
                        <td class="px-4 text-center">
                            <div class="btn-group">
                                @can('programa.show')
                                    <a href="/programa/${programa.id}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan
                                @can('programa.edit')
                                    <a href="/programa/${programa.id}/edit" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('programa.delete')
                                    <form action="/programa/${programa.id}" method="POST" class="d-inline formulario-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                `;
            });

            $('#programasTableBody').html(tableRows);

            // Re-asignar eventos de eliminación
            $('.formulario-eliminar').off('submit').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const nombre = form.closest('tr').find('td:nth-child(3)').text().trim();
                
                confirmDelete(nombre, form.attr('action'), form[0]);
            });
        }

        // Actualizar paginación
        function updatePagination(response) {
            // Implementar paginación si es necesario
            // Por ahora solo mostramos los resultados
        }
    });
</script>
@endsection