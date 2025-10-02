@extends('adminlte::page')

@section('title', 'Programas de Formación')

@section('content_header')
    <h1>
        <i class="fas fa-graduation-cap"></i>
        Programas de Formación
        @can('programa.create')
            <a href="{{ route('programa.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus"></i> Nuevo Programa
            </a>
        @endcan
    </h1>
@stop

@section('content')
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
        <!-- Filtros y búsqueda -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Filtros y Búsqueda Avanzada
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="searchForm" method="get" action="{{ route('programa.search') }}" class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <!-- Búsqueda general -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="search">
                                        <i class="fas fa-search"></i> Búsqueda General
                                    </label>
                                    <input type="text" name="search" id="search" class="form-control"
                                        placeholder="Buscar por código, nombre, red de conocimiento o nivel..." 
                                        value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            
                            <!-- Filtro por red de conocimiento -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="red_conocimiento_id">
                                        <i class="fas fa-network-wired"></i> Red de Conocimiento
                                    </label>
                                    <select name="red_conocimiento_id" id="red_conocimiento_id" class="form-control">
                                        <option value="">Todas las redes</option>
                                        @foreach(\App\Models\RedConocimiento::all() as $red)
                                            <option value="{{ $red->id }}" {{ request()->get('red_conocimiento_id') == $red->id ? 'selected' : '' }}>
                                                {{ $red->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Filtro por nivel de formación -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nivel_formacion_id">
                                        <i class="fas fa-layer-group"></i> Nivel de Formación
                                    </label>
                                    <select name="nivel_formacion_id" id="nivel_formacion_id" class="form-control">
                                        <option value="">Todos los niveles</option>
                                        @foreach(\App\Models\Parametro::whereIn('name', ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'])->get() as $nivel)
                                            <option value="{{ $nivel->id }}" {{ request()->get('nivel_formacion_id') == $nivel->id ? 'selected' : '' }}>
                                                {{ $nivel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Filtro por estado -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">
                                        <i class="fas fa-toggle-on"></i> Estado
                                    </label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Todos los estados</option>
                                        <option value="1" {{ request()->get('status') === '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ request()->get('status') === '0' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Elementos por página -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="per_page">
                                        <i class="fas fa-list"></i> Elementos por página
                                    </label>
                                    <select name="per_page" id="per_page" class="form-control">
                                        <option value="6" {{ request()->get('per_page') == 6 ? 'selected' : '' }}>6</option>
                                        <option value="12" {{ request()->get('per_page') == 12 ? 'selected' : '' }}>12</option>
                                        <option value="24" {{ request()->get('per_page') == 24 ? 'selected' : '' }}>24</option>
                                        <option value="50" {{ request()->get('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="btn-group d-block">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                        <button type="button" id="clearFilters" class="btn btn-secondary">
                                            <i class="fas fa-refresh"></i> Limpiar Filtros
                                        </button>
                                        <button type="button" id="exportResults" class="btn btn-success">
                                            <i class="fas fa-download"></i> Exportar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de programas -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Lista de Programas de Formación
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 8%">
                                    <i class="fas fa-hashtag"></i> ID
                                </th>
                                <th style="width: 12%">
                                    <i class="fas fa-code"></i> Código
                                </th>
                                <th style="width: 25%">
                                    <i class="fas fa-graduation-cap"></i> Nombre del Programa
                                </th>
                                <th style="width: 20%">
                                    <i class="fas fa-network-wired"></i> Red de Conocimiento
                                </th>
                                <th style="width: 15%">
                                    <i class="fas fa-layer-group"></i> Nivel de Formación
                                </th>
                                <th style="width: 10%">
                                    <i class="fas fa-toggle-on"></i> Estado
                                </th>
                                <th style="width: 10%">
                                    <i class="fas fa-cogs"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($programas as $programa)
                                <tr>
                                    <td>
                                        <span class="badge badge-info">{{ $programa->id }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $programa->codigo }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $programa->nombre }}</strong>
                                    </td>
                                    <td>
                                        @if($programa->redConocimiento)
                                            <span class="text-primary">
                                                <i class="fas fa-network-wired"></i> 
                                                {{ $programa->redConocimiento->nombre }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($programa->nivelFormacion)
                                            <span class="text-success">
                                                <i class="fas fa-layer-group"></i> 
                                                {{ $programa->nivelFormacion->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($programa->status)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Activo
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times"></i> Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('programa.show')
                                                <a href="{{ route('programa.show', $programa->id) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('programa.edit')
                                                <a href="{{ route('programa.edit', $programa->id) }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('programa.delete')
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm btn-delete" 
                                                        title="Eliminar"
                                                        data-id="{{ $programa->id }}"
                                                        data-name="{{ $programa->nombre }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="mb-0">No hay programas de formación disponibles.</p>
                                            @can('programa.create')
                                                <a href="{{ route('programa.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus"></i> Crear primer programa
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($programas->hasPages())
                <div class="card-footer clearfix">
                    {{ $programas->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el programa de formación <strong id="programaName"></strong>?</p>
                    <p class="text-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Variables globales
        let searchTimeout;
        let currentPage = 1;
        let isLoading = false;

        // Eliminar programa
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('name');
            
            document.getElementById('programaName').textContent = nombre;
            document.getElementById('deleteForm').action = '{{ route("programa.destroy", ":id") }}'.replace(':id', id);
            $('#deleteModal').modal('show');
        });

        // Búsqueda en tiempo real con debounce
        $('#search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performAjaxSearch();
            }, 500);
        });

        // Filtros que disparan búsqueda automática
        $('#red_conocimiento_id, #nivel_formacion_id, #status, #per_page').on('change', function() {
            performAjaxSearch();
        });

        // Limpiar filtros
        $('#clearFilters').on('click', function() {
            $('#searchForm')[0].reset();
            $('#searchForm select').val('');
            performAjaxSearch();
        });

        // Exportar resultados
        $('#exportResults').on('click', function() {
            const form = $('#searchForm');
            const action = form.attr('action');
            const data = form.serialize();
            
            // Crear enlace de descarga
            const exportUrl = action + '?' + data + '&export=excel';
            window.open(exportUrl, '_blank');
        });

        // Función para realizar búsqueda AJAX
        function performAjaxSearch() {
            if (isLoading) return;
            
            isLoading = true;
            currentPage = 1;
            
            const formData = $('#searchForm').serialize();
            
            $.ajax({
                url: '{{ route("programa.search") }}',
                method: 'GET',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function() {
                    showLoadingState();
                },
                success: function(response) {
                    if (response.success) {
                        updateTableContent(response.data);
                        updatePagination(response.data.pagination);
                        updateFilters(response.data.filters);
                    } else {
                        showError('Error en la búsqueda: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error en la búsqueda';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    isLoading = false;
                    hideLoadingState();
                }
            });
        }

        // Actualizar contenido de la tabla
        function updateTableContent(data) {
            const tbody = $('table tbody');
            tbody.empty();

            if (data.programas.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p class="mb-0">No se encontraron programas de formación.</p>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            data.programas.forEach(function(programa) {
                const row = createTableRow(programa);
                tbody.append(row);
            });

            // Re-asignar eventos de eliminación
            $('.btn-delete').off('click').on('click', function() {
                const id = $(this).data('id');
                const nombre = $(this).data('name');
                
                document.getElementById('programaName').textContent = nombre;
                document.getElementById('deleteForm').action = '{{ route("programa.destroy", ":id") }}'.replace(':id', id);
                $('#deleteModal').modal('show');
            });
        }

        // Crear fila de tabla
        function createTableRow(programa) {
            const statusBadge = programa.status ? 
                '<span class="badge badge-success"><i class="fas fa-check"></i> Activo</span>' :
                '<span class="badge badge-danger"><i class="fas fa-times"></i> Inactivo</span>';

            const redConocimiento = programa.red_conocimiento ? 
                `<span class="text-primary"><i class="fas fa-network-wired"></i> ${programa.red_conocimiento.nombre}</span>` :
                '<span class="text-muted">Sin asignar</span>';

            const nivelFormacion = programa.nivel_formacion ? 
                `<span class="text-success"><i class="fas fa-layer-group"></i> ${programa.nivel_formacion.name}</span>` :
                '<span class="text-muted">Sin asignar</span>';

            return `
                <tr>
                    <td><span class="badge badge-info">${programa.id}</span></td>
                    <td><span class="badge badge-secondary">${programa.codigo}</span></td>
                    <td><strong>${programa.nombre}</strong></td>
                    <td>${redConocimiento}</td>
                    <td>${nivelFormacion}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
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
                                <button type="button" class="btn btn-danger btn-sm btn-delete" title="Eliminar"
                                        data-id="${programa.id}" data-name="${programa.nombre}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            `;
        }

        // Actualizar paginación
        function updatePagination(pagination) {
            const paginationContainer = $('.card-footer');
            
            if (pagination.last_page <= 1) {
                paginationContainer.hide();
                return;
            }

            paginationContainer.show();
            
            let paginationHtml = '<nav><ul class="pagination justify-content-center">';
            
            // Botón anterior
            if (pagination.current_page > 1) {
                paginationHtml += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Anterior</a>
                </li>`;
            }

            // Números de página
            for (let i = 1; i <= pagination.last_page; i++) {
                const activeClass = i === pagination.current_page ? 'active' : '';
                paginationHtml += `<li class="page-item ${activeClass}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            }

            // Botón siguiente
            if (pagination.has_more_pages) {
                paginationHtml += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Siguiente</a>
                </li>`;
            }

            paginationHtml += '</ul></nav>';
            paginationContainer.html(paginationHtml);

            // Eventos de paginación
            $('.pagination a').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page && page !== currentPage) {
                    currentPage = page;
                    loadPage(page);
                }
            });
        }

        // Cargar página específica
        function loadPage(page) {
            if (isLoading) return;
            
            isLoading = true;
            const formData = $('#searchForm').serialize() + `&page=${page}`;
            
            $.ajax({
                url: '{{ route("programa.search") }}',
                method: 'GET',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                beforeSend: function() {
                    showLoadingState();
                },
                success: function(response) {
                    if (response.success) {
                        updateTableContent(response.data);
                        updatePagination(response.data.pagination);
                    }
                },
                complete: function() {
                    isLoading = false;
                    hideLoadingState();
                }
            });
        }

        // Actualizar filtros
        function updateFilters(filters) {
            $('#search').val(filters.search || '');
            $('#red_conocimiento_id').val(filters.red_conocimiento_id || '');
            $('#nivel_formacion_id').val(filters.nivel_formacion_id || '');
            $('#status').val(filters.status || '');
        }

        // Mostrar estado de carga
        function showLoadingState() {
            $('table tbody').html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2">Buscando programas...</p>
                    </td>
                </tr>
            `);
        }

        // Ocultar estado de carga
        function hideLoadingState() {
            // El contenido se actualiza en updateTableContent
        }

        // Mostrar error
        function showError(message) {
            $('table tbody').html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> ${message}
                        </div>
                    </td>
                </tr>
            `);
        }
    });
</script>
@endsection
