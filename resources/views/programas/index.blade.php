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
                            <div class="input-group w-25">
                                <form action="{{ route('programa.index') }}" method="GET" class="input-group">
                                    <input type="text" name="search" id="searchPrograma"
                                        class="form-control form-control-sm" placeholder="Buscar programa..."
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
                                            <th class="px-4 py-3" style="width: 10%">Código</th>
                                            <th class="px-4 py-3" style="width: 30%">Nombre</th>
                                            <th class="px-4 py-3" style="width: 20%">Red de Conocimiento</th>
                                            <th class="px-4 py-3" style="width: 15%">Nivel</th>
                                            <th class="px-4 py-3" style="width: 10%">Estado</th>
                                            <th class="px-4 py-3 text-center" style="width: 10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Confirmar eliminación
        $('.formulario-eliminar').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const nombre = form.closest('tr').find('td:nth-child(3)').text().trim();
            
            if (confirm(`¿Está seguro de que desea eliminar el programa "${nombre}"?`)) {
                form.off('submit').submit();
            }
        });
    });
</script>
@endsection