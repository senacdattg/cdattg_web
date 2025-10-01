@extends('adminlte::page')

@section('title', 'Aprendices')

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
                    <i class="fas fa-user-graduate text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Aprendices</h1>
                    <p class="text-muted mb-0 font-weight-light">Gestión de aprendices</p>
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
                            <i class="fas fa-user-graduate"></i> Aprendices
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
                <!-- Card de Filtros -->
                <div class="card shadow-sm mb-4 no-hover">
                    <div class="card-header bg-white py-3 d-flex align-items-center">
                        <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                            <i class="fas fa-filter mr-2"></i> Filtros de Búsqueda
                        </h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                            data-target="#filtrosForm" aria-expanded="true">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <div class="collapse show" id="filtrosForm">
                        <div class="card-body">
                            <form action="{{ route('aprendices.index') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="search" class="form-label">Buscar por nombre o documento</label>
                                            <input type="text" name="search" id="search" class="form-control" 
                                                placeholder="Ingrese nombre o número de documento" 
                                                value="{{ request('search') }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="ficha_id" class="form-label">Filtrar por ficha</label>
                                            <select name="ficha_id" id="ficha_id" class="form-control">
                                                <option value="">Todas las fichas</option>
                                                @foreach($fichas as $ficha)
                                                    <option value="{{ $ficha->id }}" 
                                                        {{ request('ficha_id') == $ficha->id ? 'selected' : '' }}>
                                                        {{ $ficha->ficha }} - {{ $ficha->programaFormacion->nombre ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Card de Lista -->
                <div class="card shadow-sm mb-4 no-hover">
                    <div class="card-header bg-white py-3 d-flex align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">Lista de Aprendices</h6>
                        @can('CREAR APRENDIZ')
                            <a href="{{ route('aprendices.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Crear Aprendiz
                            </a>
                        @endcan
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="px-4 py-3" style="width: 5%">#</th>
                                        <th class="px-4 py-3" style="width: 20%">Nombre y Apellido</th>
                                        <th class="px-4 py-3" style="width: 12%">Documento</th>
                                        <th class="px-4 py-3" style="width: 12%">Ficha Principal</th>
                                        <th class="px-4 py-3" style="width: 20%">Correo Electrónico</th>
                                        <th class="px-4 py-3" style="width: 10%">Estado</th>
                                        <th class="px-4 py-3 text-center" style="width: 21%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($aprendices as $aprendiz)
                                        <tr>
                                            <td class="px-4">{{ $loop->iteration + ($aprendices->currentPage() - 1) * $aprendices->perPage() }}</td>
                                            <td class="px-4 font-weight-medium">{{ $aprendiz->persona->nombre_completo }}</td>
                                            <td class="px-4">{{ $aprendiz->persona->numero_documento }}</td>
                                            <td class="px-4">
                                                @if($aprendiz->fichaCaracterizacion)
                                                    <span class="badge badge-info">
                                                        {{ $aprendiz->fichaCaracterizacion->ficha }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                            <td class="px-4">{{ $aprendiz->persona->email }}</td>
                                            <td class="px-4">
                                                <div
                                                    class="d-inline-block px-3 py-1 rounded-pill {{ $aprendiz->estado === 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $aprendiz->estado === 1 ? 'Activo' : 'Inactivo' }}
                                                </div>
                                            </td>
                                            <td class="px-4 text-center">
                                                <div class="btn-group">
                                                    @can('EDITAR APRENDIZ')
                                                        <form action="{{ route('aprendices.cambiarEstado', $aprendiz->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-light btn-sm" data-toggle="tooltip" title="Cambiar estado">
                                                                <i class="fas fa-sync text-success"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('VER APRENDIZ')
                                                        <a href="{{ route('aprendices.show', $aprendiz->id) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                            <i class="fas fa-eye text-warning"></i>
                                                        </a>
                                                    @endcan
                                                    @can('EDITAR APRENDIZ')
                                                        <a href="{{ route('aprendices.edit', $aprendiz->id) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                            <i class="fas fa-pencil-alt text-info"></i>
                                                        </a>
                                                    @endcan
                                                    @can('ELIMINAR APRENDIZ')
                                                        <form action="{{ route('aprendices.destroy', $aprendiz->id) }}" method="POST" class="d-inline formulario-eliminar">
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
                                                <img src="{{ asset('img/no-data.svg') }}" alt="No data" class="img-fluid" style="max-width: 200px;">
                                                <p class="text-muted mt-3">No se encontraron aprendices</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="float-right">
                            {{ $aprendices->links() }}
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
    @include('layout.alertas')
@endsection

@section('js')
    @vite(['resources/js/aprendices.js'])
@endsection

