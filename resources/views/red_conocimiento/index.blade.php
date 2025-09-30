@extends('adminlte::page')

@section('title', "Redes de Conocimiento")

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
                    <i class="fas fa-network-wired text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Redes de Conocimiento</h1>
                    <p class="text-muted mb-0 font-weight-light">Gestión de redes de conocimiento</p>
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
                            <i class="fas fa-network-wired"></i> Redes de Conocimiento
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
                <div class="card shadow-sm mb-4 no-hover">
                    <div class="card-header bg-white py-3 d-flex align-items-center">
                        <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                            <i class="fas fa-plus-circle mr-2"></i> Crear Red de Conocimiento
                        </h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                            data-target="#createRedConocimientoForm" aria-expanded="true">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <div class="collapse show" id="createRedConocimientoForm">
                        <div class="card-body">
                            @include('red_conocimiento.create')
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 no-hover">
                    <div class="card-header bg-white py-3 d-flex align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">Lista de Redes de Conocimiento</h6>
                        <div class="input-group w-25">
                            <form action="{{ route('red-conocimiento.index') }}" method="GET" class="input-group">
                                <input type="text" name="search" id="searchRedConocimiento"
                                    class="form-control form-control-sm" placeholder="Buscar red de conocimiento..."
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
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="px-4 py-3" style="width: 5%">#</th>
                                        <th class="px-4 py-3" style="width: 40%">Red de Conocimiento</th>
                                        <th class="px-4 py-3" style="width: 25%">Regional</th>
                                        <th class="px-4 py-3" style="width: 15%">Estado</th>
                                        <th class="px-4 py-3 text-center" style="width: 35%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($redesConocimiento as $redConocimiento)
                                        <tr>
                                            <td class="px-4">{{ $loop->iteration }}</td>
                                            <td class="px-4 font-weight-medium">{{ $redConocimiento->nombre }}</td>
                                            <td class="px-4 font-weight-medium">
                                                {{ $redConocimiento->regional ? $redConocimiento->regional->nombre : 'Sin regional' }}
                                            </td>
                                            <td class="px-4">
                                                <div
                                                    class="d-inline-block px-3 py-1 rounded-pill {{ $redConocimiento->status === 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $redConocimiento->status === 1 ? 'Activo' : 'Inactivo' }}
                                                </div>
                                            </td>
                                            <td class="px-4 text-center">
                                                <div class="btn-group">
                                                    @can('EDITAR RED CONOCIMIENTO')
                                                        <form action="{{ route('red-conocimiento.cambiarEstado', ['red_conocimiento' => $redConocimiento->id]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-light btn-sm" data-toggle="tooltip" title="Cambiar estado">
                                                                <i class="fas fa-sync text-success"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    <a href="{{ route('red-conocimiento.show', ['red_conocimiento' => $redConocimiento->id]) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                        <i class="fas fa-eye text-warning"></i>
                                                    </a>
                                                    @can('EDITAR RED CONOCIMIENTO')
                                                        <a href="{{ route('red-conocimiento.edit', ['red_conocimiento' => $redConocimiento->id]) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                            <i class="fas fa-pencil-alt text-info"></i>
                                                        </a>
                                                    @endcan
                                                    @can('ELIMINAR RED CONOCIMIENTO')
                                                        <form action="{{ route('red-conocimiento.destroy', ['red_conocimiento' => $redConocimiento->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta red de conocimiento?');">
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
                                            <td colspan="5" class="text-center py-5">
                                                <img src="{{ asset('img/no-data.svg') }}" alt="No data" class="img-fluid">
                                                <p class="text-muted mt-3">No se encontraron resultados</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="float-right">
                            {{ $redesConocimiento->links() }}
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
    @vite(['resources/js/red-conocimiento.js'])
@endsection
