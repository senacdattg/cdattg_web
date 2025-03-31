@extends('adminlte::page')

@section('css')
<style>
    .detail-card {
        border: none;
        box-shadow: 0 0 10px rgba(0,0,0,.05);
    }
    .detail-table th {
        width: 200px;
        background-color: #f8f9fa;
        border-right: 2px solid #fff;
    }
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 500;
    }
    .user-info {
        color: #495057;
        font-weight: 500;
    }
    .timestamp {
        color: #6c757d;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
    <section class="content-header bg-light py-3 shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0 text-gray-800">{{ $parametro->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('verificarLogin') }}" class="text-primary">Inicio</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('parametro.index') }}" class="text-primary">Parámetros</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $parametro->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('parametro.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle del Parámetro
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">{{ $parametro->name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $parametro->status === 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $parametro->status === 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Creado Por</th>
                                            <td class="py-3 user-info">
                                                @if ($parametro->userCreate)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $parametro->userCreate->persona->primer_nombre }}
                                                    {{ $parametro->userCreate->persona->primer_apellido }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Actualizado Por</th>
                                            <td class="py-3 user-info">
                                                @if ($parametro->userUpdate)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $parametro->userUpdate->persona->primer_nombre }}
                                                    {{ $parametro->userUpdate->persona->primer_apellido }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Creado en</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $parametro->created_at }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Actualizado en</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $parametro->updated_at }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR PARAMETRO')
                                    <form action="{{ route('parametro.cambiarEstado', ['parametro' => $parametro->id]) }}" method="POST" class="d-inline mx-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-sync mr-1"></i> Cambiar Estado
                                        </button>
                                    </form>
                                    <a href="{{ route('parametro.edit', ['parametro' => $parametro->id]) }}" class="btn btn-outline-info btn-sm mx-1">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                @can('ELIMINAR PARAMETRO')
                                    <form action="{{ route('parametro.destroy', ['parametro' => $parametro->id]) }}" method="POST" class="d-inline mx-1 formulario-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
