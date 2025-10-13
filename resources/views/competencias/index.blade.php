@extends('adminlte::page')

@section('css')
    @vite(['resources/css/competencias.css'])
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
        icon="fa-clipboard-list" 
        title="Competencias"
        subtitle="Gestión de competencias del SENA"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => url('/'), 'icon' => 'fa-home'],
            ['label' => 'Competencias', 'active' => true, 'icon' => 'fa-clipboard-list']
        ]"
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
                    @can('CREAR COMPETENCIA')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <a href="{{ route('competencias.create') }}" class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1 text-decoration-none">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear Competencia
                                </a>
                            </div>
                        </div>
                    @endcan

                    <!-- Filtros -->
                    <div class="card shadow-sm mb-3 no-hover">
                        <div class="card-header bg-white py-2">
                            <button class="btn btn-link btn-sm text-decoration-none p-0 m-0 w-100 text-left" type="button" data-toggle="collapse" data-target="#filtrosCollapse" aria-expanded="false">
                                <i class="fas fa-filter text-primary"></i> <strong class="text-primary">Filtros de Búsqueda</strong>
                                <i class="fas fa-chevron-down float-right mt-1"></i>
                            </button>
                        </div>
                        <div class="collapse" id="filtrosCollapse">
                            <div class="card-body">
                                <form action="{{ route('competencias.index') }}" method="GET">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="search" class="font-weight-bold">Búsqueda general</label>
                                                <input type="text" name="search" id="search" class="form-control form-control-sm" 
                                                    placeholder="Código o nombre..." value="{{ request('search') }}">
                                                <small class="text-muted">Busca en código, nombre y descripción</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status" class="font-weight-bold">Estado</label>
                                                <select name="status" id="status" class="form-control form-control-sm">
                                                    <option value="">Todos</option>
                                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activas</option>
                                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivas</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="duracion_min" class="font-weight-bold">Duración mínima (hrs)</label>
                                                <input type="number" name="duracion_min" id="duracion_min" class="form-control form-control-sm" 
                                                    placeholder="Ej: 48" value="{{ request('duracion_min') }}" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="duracion_max" class="font-weight-bold">Duración máxima (hrs)</label>
                                                <input type="number" name="duracion_max" id="duracion_max" class="form-control form-control-sm" 
                                                    placeholder="Ej: 500" value="{{ request('duracion_max') }}" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="fecha_inicio" class="font-weight-bold">Fecha inicio desde</label>
                                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control form-control-sm" 
                                                    value="{{ request('fecha_inicio') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="fecha_fin" class="font-weight-bold">Fecha fin hasta</label>
                                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control form-control-sm" 
                                                    value="{{ request('fecha_fin') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold d-block">&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-search"></i> Buscar
                                                </button>
                                                <a href="{{ route('competencias.index') }}" class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-times"></i> Limpiar
                                                </a>
                                                @if(request()->hasAny(['search', 'status', 'duracion_min', 'duracion_max', 'fecha_inicio', 'fecha_fin']))
                                                    <span class="badge badge-info ml-2">
                                                        <i class="fas fa-filter"></i> Filtros activos
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <x-data-table 
                        title="Lista de Competencias"
                        searchable="true"
                        searchAction="{{ route('competencias.index') }}"
                        searchPlaceholder="Buscar competencia..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Código', 'width' => '12%'],
                            ['label' => 'Nombre', 'width' => '33%'],
                            ['label' => 'Duración', 'width' => '10%'],
                            ['label' => 'RAPs', 'width' => '10%'],
                            ['label' => 'Estado', 'width' => '15%'],
                            ['label' => 'Acciones', 'width' => '15%', 'class' => 'text-center']
                        ]"
                        :pagination="$competencias->links()"
                    >
                                        @forelse ($competencias as $competencia)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4 font-weight-medium">{{ $competencia->codigo }}</td>
                                                <td class="px-4">{{ Str::limit($competencia->nombre, 50) }}</td>
                                                <td class="px-4">
                                                    @if($competencia->duracion)
                                                        <span class="badge badge-info">{{ formatear_horas($competencia->duracion) }}h</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 text-center">
                                                    <span class="badge badge-primary">{{ $competencia->resultadosAprendizaje->count() }}</span>
                                                </td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $competencia->status == 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $competencia->status == 1 ? 'Activa' : 'Inactiva' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('VER COMPETENCIA')
                                                            <a href="{{ route('competencias.show', $competencia) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('GESTIONAR RESULTADOS COMPETENCIA')
                                                            <a href="{{ route('competencias.gestionarResultados', $competencia) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Gestionar Resultados">
                                                                <i class="fas fa-tasks text-success"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR COMPETENCIA')
                                                            <a href="{{ route('competencias.edit', $competencia) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('ELIMINAR COMPETENCIA')
                                                            <button type="button" class="btn btn-light btn-sm" 
                                                                data-competencia="{{ $competencia->codigo }}" 
                                                                data-url="{{ route('competencias.destroy', $competencia) }}"
                                                                onclick="confirmarEliminacion(this.dataset.competencia, this.dataset.url)"
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
                                                    <p class="text-muted">No hay competencias registradas</p>
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
    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la competencia "${nombre}"? Esta acción no se puede deshacer.`,
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

    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        $('[data-toggle="tooltip"]').tooltip();

        // Abrir filtros automáticamente si hay filtros activos
        @if(request()->hasAny(['search', 'status', 'duracion_min', 'duracion_max', 'fecha_inicio', 'fecha_fin']))
            $('#filtrosCollapse').collapse('show');
        @endif

        // Cambiar ícono del chevron al expandir/colapsar
        $('#filtrosCollapse').on('show.bs.collapse', function () {
            $('[data-target="#filtrosCollapse"] .fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });
        $('#filtrosCollapse').on('hide.bs.collapse', function () {
            $('[data-target="#filtrosCollapse"] .fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        });
    });
</script>
@endsection
