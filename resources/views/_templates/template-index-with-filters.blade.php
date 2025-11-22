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

                    {{-- Panel de Filtros Colapsable --}}
                    <div class="card shadow-sm mb-3 no-hover">
                        <div class="card-header bg-white py-2">
                            <button class="btn btn-link btn-sm text-decoration-none p-0 m-0 w-100 text-left" type="button" data-toggle="collapse" data-target="#filtrosCollapse" aria-expanded="false">
                                <i class="fas fa-filter text-primary"></i> <strong class="text-primary">Filtros de Búsqueda</strong>
                                <i class="fas fa-chevron-down float-right mt-1"></i>
                            </button>
                        </div>
                        <div class="collapse" id="filtrosCollapse">
                            <div class="card-body">
                                <form action="{{ route('{module}.index') }}" method="GET">
                                    <div class="row">
                                        {{-- Búsqueda General --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="search" class="font-weight-bold">Búsqueda general</label>
                                                <input type="text" name="search" id="search" class="form-control form-control-sm" 
                                                    placeholder="Código o nombre..." value="{{ request('search') }}">
                                                <small class="text-muted">Busca en múltiples campos</small>
                                            </div>
                                        </div>

                                        {{-- Estado --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status" class="font-weight-bold">Estado</label>
                                                <select name="status" id="status" class="form-control form-control-sm">
                                                    <option value="">Todos</option>
                                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activos</option>
                                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- AGREGAR MÁS FILTROS SEGÚN TU MODELO --}}
                                        {{-- Ejemplo: Filtro por categoría, tipo, etc. --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="filtro_1" class="font-weight-bold">Filtro 1</label>
                                                <input type="text" name="filtro_1" id="filtro_1" class="form-control form-control-sm" 
                                                    placeholder="Valor..." value="{{ request('filtro_1') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="filtro_2" class="font-weight-bold">Filtro 2</label>
                                                <select name="filtro_2" id="filtro_2" class="form-control form-control-sm">
                                                    <option value="">Seleccione...</option>
                                                    {{-- Agrega opciones aquí --}}
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Botones de acción --}}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-0">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-search"></i> Buscar
                                                </button>
                                                <a href="{{ route('{module}.index') }}" class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-times"></i> Limpiar
                                                </a>
                                                @if(request()->hasAny(['search', 'status', 'filtro_1', 'filtro_2']))
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

                    {{-- Tabla Principal --}}
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lista de {Module}s</h6>
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
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection

