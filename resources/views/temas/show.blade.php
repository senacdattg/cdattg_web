@extends('adminlte::page')

@section('content')
<!-- Encabezado de la página -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $tema->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('verificarLogin') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tema.index') }}">Temas</a></li>
                    <li class="breadcrumb-item active">{{ $tema->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Contenido principal -->
<section class="content">
    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('tema.index') }}" title="Volver">
        <i class="fas fa-arrow-left"></i> Volver
    </a>

    <!-- Card de Detalle del Tema -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Detalle del Tema</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $tema->name }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge badge-{{ $tema->status === 1 ? 'success' : 'danger' }}">
                                    {{ $tema->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Creado Por:</th>
                            <td>
                                @if ($tema->userCreate)
                                {{ $tema->userCreate->persona->primer_nombre }}
                                {{ $tema->userCreate->persona->primer_apellido }}
                                @else
                                <em>Usuario no disponible</em>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Actualizado Por:</th>
                            <td>
                                @if ($tema->userUpdate)
                                {{ $tema->userUpdate->persona->primer_nombre }}
                                {{ $tema->userUpdate->persona->primer_apellido }}
                                @else
                                <em>Usuario no disponible</em>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Creado en:</th>
                            <td>{{ $tema->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Actualizado en:</th>
                            <td>{{ $tema->updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-center">
            @can('EDITAR TEMA')
            <form id="cambiarEstadoForm" class="d-inline"
                action="{{ route('tema.cambiarEstado', ['tema' => $tema->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success btn-sm" title="Cambiar Estado">
                    <i class="fas fa-sync"></i> Cambiar Estado
                </button>
            </form>
            <a class="btn btn-info btn-sm" href="{{ route('tema.edit', ['tema' => $tema->id]) }}" title="Editar">
                <i class="fas fa-pencil-alt"></i> Editar
            </a>
            @endcan
            @can('ELIMINAR TEMA')
            <form class="formulario-eliminar d-inline" action="{{ route('tema.destroy', ['tema' => $tema->id]) }}"
                method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
            @endcan
        </div>

    </div>

    <!-- Card de Parámetros Asociados -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">Parámetros Asociados</h4>
        </div>
        <div class="card-body">
            @if ($tema->parametros->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr class="text-center">
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tema->parametros as $parametro)
                        <tr class="text-center">
                            <td>{{ $parametro->name }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ $parametro->pivot->status == 1 ? 'success' : 'danger' }}">
                                    {{ $parametro->pivot->status == 1 ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            </td>
                            <td>
                                @can('EDITAR TEMA')
                                <form class="d-inline"
                                    action="{{ route('tema.cambiarEstadoParametro', ['tema' => $tema->id, 'parametro' => $parametro->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm"
                                        title="Cambiar Estado">
                                        <i class="fas fa-sync"></i> Cambiar Estado
                                    </button>
                                </form>
                                @endcan
                                @can('ELIMINAR PARAMETRO DE TEMA')
                                <form class="d-inline"
                                    action="{{ route('tema.eliminarParametro', ['tema' => $tema->id, 'parametro' => $parametro->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted text-center">No hay parámetros asignados a este tema.</p>
            @endif
        </div>
    </div>



</section>
</div>
@include('layout.footer')
@endsection