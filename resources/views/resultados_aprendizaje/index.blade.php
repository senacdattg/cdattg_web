@extends('adminlte::page')

@section('css')
    @vite(['resources/css/guias_aprendizaje.css'])
    <style>
        .dashboard-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .link_right_header {
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .link_right_header:hover {
            color: #4299e1;
        }
        .breadcrumb-item {
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .breadcrumb-item i {
            font-size: 0.8rem;
            margin-right: 0.4rem;
        }
        .breadcrumb-item a {
            color: #4a5568;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #718096;
        }
        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
        }
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-graduation-cap" 
        title="Resultados de Aprendizaje"
        subtitle="Gestión de resultados de aprendizaje del SENA"
        :breadcrumb="[['label' => 'Inicio', 'url' => '{{ url('/') }}', 'icon' => 'fa-home'], ['label' => 'Resultados de Aprendizaje', 'icon' => 'fa-graduation-cap', 'active' => true]]"
    />
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
                    @can('CREAR RESULTADO APRENDIZAJE')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <a href="{{ route('resultados-aprendizaje.create') }}" class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1 text-decoration-none">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear Resultado de Aprendizaje
                                </a>
                            </div>
                        </div>
                    @endcan

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary mb-3">Lista de Resultados de Aprendizaje</h6>
                            
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="mr-2">
                                    <select id="filterCompetencia" class="form-control form-control-sm" style="width: 200px;">
                                        <option value="">Todas las competencias</option>
                                        @php
                                            $competencias = \App\Models\Competencia::orderBy('nombre')->get();
                                        @endphp
                                        @foreach($competencias as $competencia)
                                            <option value="{{ $competencia->id }}">{{ Str::limit($competencia->nombre, 25) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mr-2">
                                    <select id="filterStatus" class="form-control form-control-sm" style="width: 100px;">
                                        <option value="">Todos</option>
                                        <option value="1">Activos</option>
                                        <option value="0">Inactivos</option>
                                    </select>
                                </div>

                                <div class="input-group" style="width: 250px;">
                                    <input type="text" id="searchRAP" class="form-control form-control-sm" 
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
                                            <th class="px-4 py-3" style="width: 15%">Código</th>
                                            <th class="px-4 py-3" style="width: 35%">Nombre</th>
                                            <th class="px-4 py-3" style="width: 10%">Duración</th>
                                            <th class="px-4 py-3" style="width: 15%">Estado</th>
                                            <th class="px-4 py-3" style="width: 10%">Guías</th>
                                            <th class="px-4 py-3 text-center" style="width: 10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($resultadosAprendizaje as $resultado)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4 font-weight-medium">{{ $resultado->codigo }}</td>
                                                <td class="px-4">{{ $resultado->nombre }}</td>
                                                <td class="px-4">
                                                    @if($resultado->duracion)
                                                        <span class="badge badge-info">{{ formatear_horas($resultado->duracion) }}h</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $resultado->status == 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $resultado->status == 1 ? 'Activo' : 'Inactivo' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <span class="badge badge-primary">{{ $resultado->guiasAprendizaje->count() }}</span>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('VER RESULTADO APRENDIZAJE')
                                                            <a href="{{ route('resultados-aprendizaje.show', $resultado) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR RESULTADO APRENDIZAJE')
                                                            <a href="{{ route('resultados-aprendizaje.edit', $resultado) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('ELIMINAR RESULTADO APRENDIZAJE')
                                                            <button type="button" class="btn btn-light btn-sm" 
                                                                data-rap="{{ $resultado->codigo }}" 
                                                                data-url="{{ route('resultados-aprendizaje.destroy', $resultado) }}"
                                                                onclick="confirmarEliminacion(this.dataset.rap, this.dataset.url)"
                                                                data-toggle="tooltip" title="Eliminar">
                                                                <i class="fas fa-trash text-danger"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data" 
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay resultados de aprendizaje registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="float-right">
                                {{ $resultadosAprendizaje->links() }}
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
        $('[data-toggle="tooltip"]').tooltip();

        // Auto-dismiss de alertas después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Buscar al presionar Enter en el campo de búsqueda
        $('#searchRAP').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                performSearch();
            }
        });

        // Buscar al hacer clic en el botón
        $('#btnSearch').on('click', function() {
            performSearch();
        });

        // Limpiar todos los filtros
        $('#btnClearFilters').on('click', function() {
            $('#searchRAP').val('');
            $('#filterCompetencia').val('');
            $('#filterStatus').val('');
            window.location.href = '{{ route("resultados-aprendizaje.index") }}';
        });

        // Función para realizar la búsqueda
        function performSearch() {
            const searchTerm = $('#searchRAP').val();
            const competenciaId = $('#filterCompetencia').val();
            const status = $('#filterStatus').val();

            let url = '{{ route("resultados-aprendizaje.index") }}?';
            const params = [];

            if (searchTerm) params.push(`search=${encodeURIComponent(searchTerm)}`);
            if (competenciaId) params.push(`competencia_id=${competenciaId}`);
            if (status !== '') params.push(`status=${status}`);

            if (params.length > 0) {
                url += params.join('&');
            }
            
            window.location.href = url;
        }

        // Mantener los valores de los filtros de la URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) $('#searchRAP').val(urlParams.get('search'));
        if (urlParams.has('competencia_id')) $('#filterCompetencia').val(urlParams.get('competencia_id'));
        if (urlParams.has('status')) $('#filterStatus').val(urlParams.get('status'));
    });

    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar el resultado "${nombre}"? Esta acción no se puede deshacer.`,
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

