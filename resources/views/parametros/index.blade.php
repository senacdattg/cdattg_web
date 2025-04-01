@extends('adminlte::page')

@section('css')
<style>
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .btn-light {
        background-color: #f8f9fa;
        border-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .btn-light:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
        transform: translateY(-1px);
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .btn-group .btn {
        margin: 0 2px;
    }

    .rounded-pill {
        font-size: 0.875rem;
    }

    .form-control {
        border-radius: 0.25rem;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 1rem;
    }

    .form-control:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
    }

    .form-label {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: #4a5568;
    }

    .btn-outline-primary {
        color: #4299e1;
        border-color: #4299e1;
    }

    .btn-outline-primary:hover {
        background-color: #4299e1;
        color: white;
    }

    .card-header .btn-sm {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dashboard-header {
        background: linear-gradient(to right, #fff, #f8f9fa);
        border-bottom: 1px solid rgba(0, 0, 0, .05);
        box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
    }
</style>
@endsection

@section('content')
<section class="content-header dashboard-header py-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 d-flex align-items-center">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px;">
                    <i class="fas fa-cogs text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Parámetros</h1>
                    <p class="text-muted mb-0 font-weight-light">Gestión de parámetros del sistema</p>
                </div>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('verificarLogin') }}" class="text-primary">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-cog"></i> Parámetros
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="content mt-4">
    <div class="container-fluid">
        @can('CREAR PARAMETRO')
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex align-items-center">
                <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                    <i class="fas fa-plus-circle mr-2"></i> Crear Parámetro
                </h5>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse" data-target="#createParameterForm" aria-expanded="true">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>

            <div class="collapse show" id="createParameterForm">
                <div class="card-body">
                    @include('parametros.create')
                </div>
            </div>
        </div>
        @endcan

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center">
                <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">Lista de Parámetros</h6>
                <div class="input-group w-25">
                    <form action="{{ route('parametro.index') }}" method="GET" class="input-group">
                        <input type="text" name="search" id="searchParameter" class="form-control form-control-sm" 
                               placeholder="Buscar parámetro..." value="{{ request('search') }}" autocomplete="off">
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
                                <th class="px-4 py-3" style="width: 40%">Nombre</th>
                                <th class="px-4 py-3" style="width: 20%">Estado</th>
                                <th class="px-4 py-3 text-center" style="width: 35%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($parametros as $parametro)
                            <tr>
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td class="px-4 font-weight-medium">{{ $parametro->name }}</td>
                                <td class="px-4">
                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $parametro->status === 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                        {{ $parametro->status === 1 ? 'Activo' : 'Inactivo' }}
                                    </div>
                                </td>
                                <td class="px-4 text-center">
                                    <div class="btn-group">
                                        @can('EDITAR PARAMETRO')
                                        <form action="{{ route('parametro.cambiarEstado', ['parametro' => $parametro->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-light btn-sm" data-toggle="tooltip" title="Cambiar estado">
                                                <i class="fas fa-sync text-success"></i>
                                            </button>
                                        </form>
                                        @endcan
                                        @can('VER PARAMETRO')
                                        <a href="{{ route('parametro.show', ['parametro' => $parametro->id]) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye text-warning"></i>
                                        </a>
                                        @endcan
                                        @can('EDITAR PARAMETRO')
                                        <a href="{{ route('parametro.edit', ['parametro' => $parametro->id]) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-pencil-alt text-info"></i>
                                        </a>
                                        @endcan
                                        @can('ELIMINAR PARAMETRO')
                                        <form action="{{ route('parametro.destroy', ['parametro' => $parametro->id]) }}" method="POST" class="d-inline formulario-eliminar">
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
                                <td colspan="4" class="text-center py-5">
                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data" style="width: 120px" class="mb-3">
                                    <p class="text-muted">No hay parámetros registrados</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white">
                <div class="float-right">
                    {{ $parametros->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();

        // Smooth collapse animation
        $('.collapse').on('show.bs.collapse hide.bs.collapse', function(e) {
            const icon = $(this).prev().find('i');
            if (e.type === 'show') {
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });

        // Search functionality
        $('#searchParameter').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Search button click handler
        $('#searchBtn').on('click', function() {
            let value = $('#searchParameter').val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
@endsection

@section('css')
<style>
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .btn-light {
        background-color: #f8f9fa;
        border-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .btn-light:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
        transform: translateY(-1px);
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .btn-group .btn {
        margin: 0 2px;
    }

    .rounded-pill {
        font-size: 0.875rem;
    }

    .form-control {
        border-radius: 0.25rem;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 1rem;
    }

    .form-control:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
    }

    .form-label {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: #4a5568;
    }

    .btn-outline-primary {
        color: #4299e1;
        border-color: #4299e1;
    }

    .btn-outline-primary:hover {
        background-color: #4299e1;
        color: white;
    }

    .card-header .btn-sm {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: "›";
        font-size: 1.2rem;
        line-height: 1;
        color: #6c757d;
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
</style>
@endsection