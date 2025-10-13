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
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-book-open" 
        title="Guías de Aprendizaje"
        subtitle="Gestión de guías de aprendizaje del SENA"
        :breadcrumb="[['label' => 'Inicio', 'url' => '{{ url('/') }}', 'icon' => 'fa-home'], ['label' => 'Guías de Aprendizaje', 'icon' => 'fa-book-open', 'active' => true]]"
    />
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
                    @can('CREAR GUIA APRENDIZAJE')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <a href="{{ route('guias-aprendizaje.create') }}" class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1 text-decoration-none">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear Guía de Aprendizaje
                                </a>
                            </div>
                        </div>
                    @endcan

                    {{-- Tabla Principal con Filtros --}}
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary mb-3">Lista de Guías de Aprendizaje</h6>
                            
                            {{-- Filtros Avanzados --}}
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                {{-- Filtro por Programa de Formación --}}
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

                                {{-- Filtro por Competencia --}}
                                <div class="mr-2">
                                    <select id="filterCompetencia" class="form-control form-control-sm" style="width: 180px;">
                                        <option value="">Todas las competencias</option>
                                        @php
                                            $competencias = \App\Models\Competencia::orderBy('nombre')->get();
                                        @endphp
                                        @foreach($competencias as $competencia)
                                            <option value="{{ $competencia->id }}">{{ Str::limit($competencia->nombre, 22) }}</option>
                                        @endforeach
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
                                    <input type="text" id="searchGuia" class="form-control form-control-sm" 
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

                        <x-data-table 
                            title="Lista de Guías de Aprendizaje"
                            searchable="true"
                            searchAction="{{ route('guiasAprendizaje.index') }}"
                            searchPlaceholder="Buscar por código, nombre..."
                            searchValue="{{ request('search') }}"
                            :columns="[
                                ['label' => '#', 'width' => '5%'],
                                ['label' => 'Código', 'width' => '15%'],
                                ['label' => 'Nombre', 'width' => '30%'],
                                ['label' => 'Estado', 'width' => '15%'],
                                ['label' => 'Resultados', 'width' => '10%'],
                                ['label' => 'Actividades', 'width' => '10%'],
                                ['label' => 'Acciones', 'width' => '15%', 'class' => 'text-center']
                            ]"
                            :pagination="$guiasAprendizaje->links()"
                        >
                                        @forelse ($guiasAprendizaje as $guia)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4 font-weight-medium">{{ $guia->codigo }}</td>
                                                <td class="px-4">{{ $guia->nombre }}</td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $guia->status == 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $guia->status == 1 ? 'Activo' : 'Inactivo' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    @if($guia->resultadosAprendizaje && $guia->resultadosAprendizaje->count() > 0)
                                                        <span class="badge badge-primary">{{ $guia->resultadosAprendizaje->count() }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">0</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 text-center">
                                                    @if($guia->actividades && $guia->actividades->count() > 0)
                                                        <span class="badge badge-warning">{{ $guia->actividades->count() }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">0</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('VER GUIA APRENDIZAJE')
                                                            <a href="{{ route('guias-aprendizaje.show', $guia) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR GUIA APRENDIZAJE')
                                                            <a href="{{ route('guias-aprendizaje.edit', $guia) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('ELIMINAR GUIA APRENDIZAJE')
                                                            <button type="button" class="btn btn-light btn-sm" 
                                                                data-guia="{{ $guia->codigo }}" 
                                                                data-url="{{ route('guias-aprendizaje.destroy', $guia) }}"
                                                                onclick="confirmarEliminacion(this.dataset.guia, this.dataset.url)"
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
                                                    <p class="text-muted">No hay guías de aprendizaje registradas</p>
                                                </td>
                                            </tr>
                                        @endforelse
                        </x-data-table>
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
        $('#searchGuia').on('input', function() {
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
            $('#searchGuia').val('');
            $('#filterPrograma').val('');
            $('#filterCompetencia').val('');
            $('#filterStatus').val('');
            window.location.href = '{{ route("guias-aprendizaje.index") }}';
        });

        // Cambios en filtros
        $('#filterPrograma, #filterCompetencia, #filterStatus').on('change', function() {
            performSearch();
        });

        // Función para realizar búsqueda
        function performSearch() {
            const searchTerm = $('#searchGuia').val();
            const programaId = $('#filterPrograma').val();
            const competenciaId = $('#filterCompetencia').val();
            const status = $('#filterStatus').val();

            // Construir URL con parámetros
            let url = '{{ route("guias-aprendizaje.index") }}?';
            const params = [];

            if (searchTerm) params.push(`search=${encodeURIComponent(searchTerm)}`);
            if (programaId) params.push(`programa_id=${programaId}`);
            if (competenciaId) params.push(`competencia_id=${competenciaId}`);
            if (status !== '') params.push(`status=${status}`);

            if (params.length > 0) {
                url += params.join('&');
                window.location.href = url;
            }
        }

        // Restaurar valores de filtros desde URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) $('#searchGuia').val(urlParams.get('search'));
        if (urlParams.has('programa_id')) $('#filterPrograma').val(urlParams.get('programa_id'));
        if (urlParams.has('competencia_id')) $('#filterCompetencia').val(urlParams.get('competencia_id'));
        if (urlParams.has('status')) $('#filterStatus').val(urlParams.get('status'));
    });

    // Confirmación de eliminación
    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la guía "${nombre}"? Esta acción no se puede deshacer.`,
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
