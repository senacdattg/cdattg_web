@extends('adminlte::page')

@section('content')
        <!-- Encabezado de la página -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Personas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item active">Personas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenido principal -->
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Personas</h3>
                    <div class="card-tools">
                        @can('CREAR PERSONA')
                            <a href="{{ route('personas.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Crear Persona
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped projects text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre y Apellido</th>
                                    <th>Número de Documento</th>
                                    <th>Correo Electrónico</th>
                                    <th>Estado</th>
                                    <th colspan="4">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($personas as $persona)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $persona->nombre_completo }}</td>
                                        <td>{{ $persona->numero_documento }}</td>
                                        <td>{{ $persona->email }}</td>
                                        <td class="project-state">
                                            <span class="badge badge-{{ $persona->status === 1 ? 'success' : 'danger' }}">
                                                {{ $persona->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                            </span>
                                        </td>
                                        <td class="project-actions">
                                            @can('CAMBIAR ESTADO PERSONA')
                                                <form class="d-inline"
                                                    action="{{ route('persona.cambiarEstadoPersona', $persona->id) }}"
                                                    method="POST" title="Cambiar Estado">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('VER PERSONA')
                                                <a href="{{ route('personas.show', $persona->id) }}"
                                                    class="btn btn-warning btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('EDITAR PERSONA')
                                                <a href="{{ route('personas.edit', $persona->id) }}"
                                                    class="btn btn-info btn-sm" title="Editar">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            @endcan
                                            @can('ELIMINAR PERSONA')
                                                <form class="d-inline eliminar-persona-form"
                                                    action="{{ route('personas.destroy', $persona->id) }}" method="POST"
                                                    title="Eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No hay personas registradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    <div class="float-right">
                        {{ $personas->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('components.confirm-delete-modal')
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.eliminar-persona-form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const nombre = form.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
                    confirmDelete(nombre, form.action, form);
                });
            });
        });
    </script>
@endsection
