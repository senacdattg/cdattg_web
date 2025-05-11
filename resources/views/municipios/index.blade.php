@extends('adminlte::page')

@section('title', 'Municipios')

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
                    <i class="fas fa-cogs text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Municipios</h1>
                    <p class="text-muted mb-0 font-weight-light">Gestión de municipios</p>
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
                            <i class="fas fa-cog"></i> Municipios
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
                                <i class="fas fa-plus-circle mr-2"></i> Crear Municipio
                            </h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                                data-target="#createParameterForm" aria-expanded="true">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>

                        <div class="collapse show" id="createParameterForm">
                            <div class="card-body">
                                @include('municipios.create')
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4 no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">Lista de Municipios</h6>
                            <div class="input-group w-25">
                                <form action="{{ route('municipio.index') }}" method="GET" class="input-group">
                                    <input type="text" name="search" id="searchMunicipio"
                                        class="form-control form-control-sm" placeholder="Buscar municipio..."
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
                                            <th class="px-4 py-3" style="width: 40%">Municipio</th>
                                            <th class="px-4 py-3" style="width: 40%">Departamento</th>
                                            <th class="px-4 py-3" style="width: 20%">Estado</th>
                                            <th class="px-4 py-3 text-center" style="width: 35%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($municipios as $municipio)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4 font-weight-medium">{{ $municipio->municipio }}</td>
                                                <td class="px-4 font-weight-medium">{{ $municipio->departamento->departamento }}</td>
                                                <td class="px-4">
                                                    <div
                                                        class="d-inline-block px-3 py-1 rounded-pill {{ $municipio->status === 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $municipio->status === 1 ? 'Activo' : 'Inactivo' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        <form
                                                            action="{{ route('municipio.cambiarEstado', ['municipio' => $municipio->id]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                                <button type="submit" class="btn btn-light btn-sm"
                                                                    data-toggle="tooltip" title="Cambiar estado">
                                                                    <i class="fas fa-sync text-success"></i>
                                                                </button>
                                                            </form>
                                                            <a href="{{ route('municipio.show', ['municipio' => $municipio->id]) }}"
                                                                class="btn btn-light btn-sm" data-toggle="tooltip"
                                                                title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                            <a href="{{ route('municipio.edit', ['municipio' => $municipio->id]) }}"
                                                                class="btn btn-light btn-sm" data-toggle="tooltip"
                                                                title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                            <form
                                                                action="{{ route('municipio.destroy', ['municipio' => $municipio->id]) }}"
                                                                method="POST" class="d-inline formulario-eliminar">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light btn-sm"
                                                                    data-toggle="tooltip" title="Eliminar">
                                                                    <i class="fas fa-trash text-danger"></i>
                                                                </button>
                                                            </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data"
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay municipios registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="float-right">
                                {{ $municipios->links() }}
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
    @vite(['resources/js/municipios.js'])
@endsection

