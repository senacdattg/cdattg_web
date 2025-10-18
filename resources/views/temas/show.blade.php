@extends('adminlte::page')

@section('css')
    @vite(['resources/css/temas.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Temas"
        subtitle="Detalles del tema"
        :breadcrumb="[['label' => 'Temas', 'url' => route('tema.index') , 'icon' => 'fa-fw fa-paint-brush'], ['label' => 'Detalles del tema', 'icon' => 'fa-info-circle', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('tema.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle del tema
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">{{ $tema->name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span
                                                    class="status-badge {{ $tema->status === 1 ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $tema->status === 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que crea</th>
                                            <td class="py-3 user-info">
                                                @if ($tema->userCreate)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $tema->userCreate->persona->primer_nombre }}
                                                    {{ $tema->userCreate->persona->primer_apellido }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de creación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $tema->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que modifica</th>
                                            <td class="py-3 user-info">
                                                @if ($tema->userUpdate)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $tema->userUpdate->persona->primer_nombre }}
                                                    {{ $tema->userUpdate->persona->primer_apellido }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de modificación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $tema->updated_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR TEMA')
                                    <form action="{{ route('tema.cambiarEstado', ['tema' => $tema->id]) }}" method="POST"
                                        class="d-inline mx-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-sync mr-1"></i> Cambiar Estado
                                        </button>
                                    </form>
                                    <a href="{{ route('tema.edit', ['tema' => $tema->id]) }}"
                                        class="btn btn-outline-info btn-sm mx-1">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                @can('ELIMINAR TEMA')
                                    <form action="{{ route('tema.destroy', ['tema' => $tema->id]) }}" method="POST"
                                        class="d-inline mx-1 formulario-eliminar">
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
                    <div class="card mb-4 shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-list-alt mr-2"></i>Parámetros Asociados
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($tema->parametros->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3" style="width: 5%">#</th>
                                                <th class="px-4 py-3" style="width: 40%">Nombre</th>
                                                <th class="px-4 py-3" style="width: 20%">Estado</th>
                                                <th class="px-4 py-3 text-center" style="width: 35%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tema->parametros as $parametro)
                                                <tr>
                                                    <td class="px-4">{{ $loop->iteration }}</td>
                                                    <td class="pl-4 align-middle">
                                                        <span class="font-weight-medium">{{ $parametro->name }}</span>
                                                    </td>
                                                    <td class="pl-3 align-middle">
                                                        <span
                                                            class="d-inline-block px-3 py-1 rounded-pill {{ $parametro->pivot->status == 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                            <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                            {{ $parametro->pivot->status == 1 ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </td>
                                                    <td class="pl-5 align-middle">
                                                        <div class="btn-group" role="group">
                                                            @can('EDITAR TEMA')
                                                                <form class="d-inline mx-1"
                                                                    action="{{ route('tema.cambiarEstadoParametro', ['tema' => $tema->id, 'parametro' => $parametro->id]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-outline-success btn-sm">
                                                                        <i class="fas fa-sync-alt mr-1"></i> Cambiar Estado
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                            @can('ELIMINAR PARAMETRO DE TEMA')
                                                                <form class="d-inline mx-1 formulario-eliminar"
                                                                    action="{{ route('tema.eliminarParametro', ['tema' => $tema->id, 'parametro' => $parametro->id]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-outline-danger btn-sm">
                                                                        <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="py-4">
                                    <i class="fas fa-clipboard-list text-muted mb-2" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mb-0">No hay parámetros asignados a este tema.</p>
                                </div>
                            @endif
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
