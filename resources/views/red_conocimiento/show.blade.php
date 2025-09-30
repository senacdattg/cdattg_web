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
                        <i class="fas fa-network-wired text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Red de Conocimiento</h1>
                        <p class="text-muted mb-0 font-weight-light">Detalles de la red de conocimiento</p>
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('red-conocimiento.index') }}" class="link_right_header">
                                    <i class="fas fa-network-wired"></i> Redes de Conocimiento
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-info-circle"></i> Detalles de la red de conocimiento
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
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('red-conocimiento.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle de la Red de Conocimiento
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">{{ $redConocimiento->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Regional</th>
                                            <td class="py-3">
                                                @if ($redConocimiento->regional)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $redConocimiento->regional->nombre }}
                                                @else
                                                    <span class="text-muted">Sin regional asignada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $redConocimiento->status === 1 ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $redConocimiento->status === 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Programas de Formación</th>
                                            <td class="py-3">
                                                @if ($redConocimiento->programasFormacion->count() > 0)
                                                    <span class="badge badge-info">{{ $redConocimiento->programasFormacion->count() }} programa(s)</span>
                                                @else
                                                    <span class="text-muted">Sin programas asignados</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que crea</th>
                                            <td class="py-3 user-info">
                                                @if ($redConocimiento->userCreated)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $redConocimiento->userCreated->persona->primer_nombre ?? 'Usuario' }}
                                                    {{ $redConocimiento->userCreated->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de creación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $redConocimiento->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que modifica</th>
                                            <td class="py-3 user-info">
                                                @if ($redConocimiento->userEdited)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $redConocimiento->userEdited->persona->primer_nombre ?? 'Usuario' }}
                                                    {{ $redConocimiento->userEdited->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de modificación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $redConocimiento->updated_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR RED CONOCIMIENTO')
                                    <form action="{{ route('red-conocimiento.cambiarEstado', ['red_conocimiento' => $redConocimiento->id]) }}"
                                        method="POST" class="d-inline mx-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-sync mr-1"></i> Cambiar Estado
                                        </button>
                                    </form>
                                    <a href="{{ route('red-conocimiento.edit', ['red_conocimiento' => $redConocimiento->id]) }}"
                                        class="btn btn-outline-info btn-sm mx-1">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                @can('ELIMINAR RED CONOCIMIENTO')
                                    <form action="{{ route('red-conocimiento.destroy', ['red_conocimiento' => $redConocimiento->id]) }}"
                                        method="POST" class="d-inline mx-1 formulario-eliminar">
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

                    <!-- Card de Programas de Formación -->
                    @if ($redConocimiento->programasFormacion->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Programas de Formación Asociados</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Nivel de Formación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($redConocimiento->programasFormacion as $programa)
                                            <tr>
                                                <td>{{ $programa->codigo }}</td>
                                                <td>{{ $programa->nombre }}</td>
                                                <td>
                                                    @if ($programa->nivelFormacion)
                                                        {{ $programa->nivelFormacion->valor }}
                                                    @else
                                                        <span class="text-muted">No asignado</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
    @include('layout.alertas')
@endsection
