@extends('adminlte::page')

@section('title', "Ver Red de Conocimiento")

@section('content')
        <!-- Encabezado de la Página -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $redConocimiento->nombre }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('red-conocimiento.index') }}">Redes de Conocimiento</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $redConocimiento->nombre }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenido Principal -->
        <section class="content">
            <div class="container-fluid">
                <!-- Botón Volver -->
                <div class="mb-3">
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('red-conocimiento.index') }}" title="Volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <!-- Card de Detalles de Red de Conocimiento -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detalles de la Red de Conocimiento</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 30%">Nombre:</th>
                                        <td>{{ $redConocimiento->nombre }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Regional:</th>
                                        <td>
                                            @if ($redConocimiento->regional)
                                                {{ $redConocimiento->regional->nombre }}
                                            @else
                                                <span class="text-muted">Sin regional asignada</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Estado:</th>
                                        <td>
                                            <span class="badge badge-{{ $redConocimiento->status === 1 ? 'success' : 'danger' }}">
                                                {{ $redConocimiento->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Programas de Formación:</th>
                                        <td>
                                            @if ($redConocimiento->programasFormacion->count() > 0)
                                                <span class="badge badge-info">{{ $redConocimiento->programasFormacion->count() }} programa(s)</span>
                                            @else
                                                <span class="text-muted">Sin programas asignados</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Creado Por:</th>
                                        <td>
                                            @if ($redConocimiento->userCreated)
                                                {{ $redConocimiento->userCreated->persona->primer_nombre ?? 'Usuario' }}
                                                {{ $redConocimiento->userCreated->persona->primer_apellido ?? '' }}
                                            @else
                                                <span class="text-muted">Usuario no disponible</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Actualizado Por:</th>
                                        <td>
                                            @if ($redConocimiento->userEdited)
                                                {{ $redConocimiento->userEdited->persona->primer_nombre ?? 'Usuario' }}
                                                {{ $redConocimiento->userEdited->persona->primer_apellido ?? '' }}
                                            @else
                                                <span class="text-muted">Usuario no disponible</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Creado en:</th>
                                        <td>{{ $redConocimiento->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Actualizado en:</th>
                                        <td>{{ $redConocimiento->updated_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Bloque de Acciones -->
                    <div class="card-footer text-center">
                        @can('EDITAR RED CONOCIMIENTO')
                            <form class="d-inline" action="{{ route('red-conocimiento.cambiarEstado', $redConocimiento->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm" title="Cambiar Estado">
                                    <i class="fas fa-sync"></i> Cambiar Estado
                                </button>
                            </form>
                            <a class="btn btn-info btn-sm" href="{{ route('red-conocimiento.edit', $redConocimiento->id) }}" title="Editar">
                                <i class="fas fa-pencil-alt"></i> Editar
                            </a>
                        @endcan
                        @can('ELIMINAR RED CONOCIMIENTO')
                            <form class="d-inline" action="{{ route('red-conocimiento.destroy', $redConocimiento->id) }}" method="POST"
                                onsubmit="return confirm('¿Está seguro de eliminar esta red de conocimiento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        @endcan
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
        </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection
