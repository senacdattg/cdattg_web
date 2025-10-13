@extends('adminlte::page')

@section('content')
        <!-- Encabezado de la página -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Centros de Formación</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item active">Centros de Formación</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenido principal -->
        <section class="content">
            @can('CREAR CENTRO DE FORMACION')
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Crear Centro de Formación</h5>
                    </div>
                    <div class="card-body">
                        @include('centros.create')
                    </div>
                </div>
            @endcan

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped projects">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>Regional</th>
                                <th>Centro de Formación</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($centros as $centro)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $centro->regional->nombre }}</td>
                                    <td>{{ $centro->nombre }}</td>
                                    <td class="project-state">
                                        <span class="badge badge-{{ $centro->status === 1 ? 'success' : 'danger' }}">
                                            {{ $centro->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                        </span>
                                    </td>
                                    <td class="project-actions">
                                        @can('EDITAR CENTRO DE FORMACION')
                                            <form class="d-inline" action="{{ route('centro.cambiarEstado', $centro->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success btn-sm" title="Cambiar Estado">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        @can('VER CENTRO DE FORMACION')
                                            <a class="btn btn-warning btn-sm"
                                                href="{{ route('centroFormacion.show', $centro->id) }}" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR CENTRO DE FORMACION')
                                            <a class="btn btn-info btn-sm" href="{{ route('centros.edit', $centro->id) }}"
                                                title="Editar">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR CENTRO DE FORMACION')
                                            <form class="d-inline eliminar-centro-form"
                                                action="{{ route('centros.destroy', $centro->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="4">No hay centros de formación registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    <div class="float-right">
                        {{ $centros->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('components.confirm-delete-modal')
@endsection

@section('js')
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection
