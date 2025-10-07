@extends('adminlte::page')

{{-- 
    PLANTILLA DE INDEX CON FILTROS AVANZADOS
    Reemplazar:
    - {module} = nombre del módulo (ej: usuarios, productos)
    - {Module} = nombre en singular capitalizado (ej: Usuario, Producto)
    - {icon} = icono FontAwesome (ej: fa-users, fa-box)
    - {permission-prefix} = prefijo del permiso (ej: USUARIO, PRODUCTO)
    - $items = colección de elementos
    - $item = elemento individual
    - Agregar/quitar filtros según necesites
--}}

@section('css')
    @vite(['resources/css/{module}.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas {icon} text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">{Module}s</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de {module}s del sistema</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas {icon}"></i> {Module}s
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
            {{-- Alertas --}}
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
                    {{-- Botón de Crear --}}
                    @can('CREAR {permission-prefix}')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <a href="{{ route('{module}.create') }}" class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1 text-decoration-none">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear {Module}
                                </a>
                            </div>
                        </div>
                    @endcan

                    {{-- Tabla Principal con Filtros --}}
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary mb-3">Lista de {Module}s</h6>
                            
                            {{-- Filtros Avanzados --}}
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                {{-- EJEMPLO: Filtro por Programa de Formación --}}
                                <div class="mr-2">
                                    <select id="filterPrograma" class="form-control form-control-sm" style="width: 200px;">
                                        <option value="">Todos los programas</option>
                                        @php
                                            $programas = \App\Models\ProgramaFormacion::orderBy('nombre')->get();
                                        @endphp
                                        @foreach($programas as $programa)
                                            <option value="{{ $programa->id }}">{{ Str::limit($programa->nombre, 25) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- EJEMPLO: Filtro por Categoría/Tipo --}}
                                <div class="mr-2">
                                    <select id="filterCategoria" class="form-control form-control-sm" style="width: 180px;">
                                        <option value="">Todas las categorías</option>
                                        {{-- Agrega tus opciones aquí --}}
                                    </select>
                                </div>

                                {{-- Filtro por Estado --}}
                                <div class="mr-2">
                                    <select id="filterStatus" class="form-control form-control-sm" style="width: 100px;">
                                        <option value="">Todos</option>
                                        <option value="1">Activos</option>
                                        <option value="0">Inactivos</option>
                                    </select>
                                </div>

                                {{-- Barra de Búsqueda --}}
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" id="searchInput" class="form-control form-control-sm" 
                                           placeholder="Buscar..." autocomplete="off">
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
                                            <th class="px-4 py-3" style="width: 30%">Nombre</th>
                                            <th class="px-4 py-3" style="width: 20%">Estado</th>
                                            <th class="px-4 py-3 text-center" style="width: 15%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @forelse ($items as $item)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4 font-weight-medium">{{ $item->nombre }}</td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $item->status == 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $item->status == 1 ? 'Activo' : 'Inactivo' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('VER {permission-prefix}')
                                                            <a href="{{ route('{module}.show', $item) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR {permission-prefix}')
                                                            <a href="{{ route('{module}.edit', $item) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('ELIMINAR {permission-prefix}')
                                                            <button type="button" class="btn btn-light btn-sm" 
                                                                data-nombre="{{ $item->nombre }}" 
                                                                data-url="{{ route('{module}.destroy', $item) }}"
                                                                onclick="confirmarEliminacion(this.dataset.nombre, this.dataset.url)"
                                                                data-toggle="tooltip" title="Eliminar">
                                                                <i class="fas fa-trash text-danger"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data" 
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay {module}s registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="float-right">
                                {{ $items->links() }}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let searchTimeout;
        
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Búsqueda en tiempo real con debounce
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performSearch();
            }, 500);
        });

        // Botón de búsqueda
        $('#btnSearch').on('click', function() {
            performSearch();
        });

        // Limpiar filtros
        $('#btnClearFilters').on('click', function() {
            $('#searchInput').val('');
            $('#filterPrograma').val('');
            $('#filterCategoria').val('');
            $('#filterStatus').val('');
            window.location.href = '{{ route("{module}.index") }}';
        });

        // Cambios en filtros
        $('#filterPrograma, #filterCategoria, #filterStatus').on('change', function() {
            performSearch();
        });

        // Función para realizar búsqueda
        function performSearch() {
            const searchTerm = $('#searchInput').val();
            const programaId = $('#filterPrograma').val();
            const categoriaId = $('#filterCategoria').val();
            const status = $('#filterStatus').val();

            // Construir URL con parámetros
            let url = '{{ route("{module}.index") }}?';
            const params = [];

            if (searchTerm) params.push(`search=${encodeURIComponent(searchTerm)}`);
            if (programaId) params.push(`programa_id=${programaId}`);
            if (categoriaId) params.push(`categoria_id=${categoriaId}`);
            if (status !== '') params.push(`status=${status}`);

            if (params.length > 0) {
                url += params.join('&');
                window.location.href = url;
            }
        }

        // Restaurar valores de filtros desde URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) $('#searchInput').val(urlParams.get('search'));
        if (urlParams.has('programa_id')) $('#filterPrograma').val(urlParams.get('programa_id'));
        if (urlParams.has('categoria_id')) $('#filterCategoria').val(urlParams.get('categoria_id'));
        if (urlParams.has('status')) $('#filterStatus').val(urlParams.get('status'));
    });

    // Confirmación de eliminación
    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar "${nombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection

