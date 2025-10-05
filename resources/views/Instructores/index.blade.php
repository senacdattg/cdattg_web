@extends('adminlte::page')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-chalkboard-teacher text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Instructores</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de instructores del sistema</p>
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
                                <i class="fas fa-chalkboard-teacher"></i> Instructores
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
                    @can('CREAR INSTRUCTOR')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear Instructor
                                </h5>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                                    data-target="#createInstructorForm" aria-expanded="false">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>

                            <div class="collapse" id="createInstructorForm">
                                <div class="card-body">
                                    @include('Instructores.create')
                                </div>
                            </div>
                        </div>
                    @endcan

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">Lista de Instructores</h6>
                            <div class="input-group w-25">
                                <form action="{{ route('instructor.index') }}" method="GET" class="input-group">
                                    <input type="text" name="search" id="searchInstructor"
                                        class="form-control form-control-sm" placeholder="Buscar instructor..."
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
                                            <th class="px-4 py-3" style="width: 25%">Nombre</th>
                                            <th class="px-4 py-3" style="width: 15%">Documento</th>
                                            <th class="px-4 py-3" style="width: 20%">Especialidades</th>
                                            <th class="px-4 py-3" style="width: 10%">Estado</th>
                                            <th class="px-4 py-3 text-center" style="width: 25%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($instructores as $instructor)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4 font-weight-medium">
                                                    {{ $instructor->persona->primer_nombre }} 
                                                    {{ $instructor->persona->primer_apellido }}
                                                </td>
                                                <td class="px-4">{{ $instructor->persona->numero_documento }}</td>
                                                <td class="px-4">
                                                    @php
                                                        $especialidades = $instructor->especialidades ?? [];
                                                        $especialidadPrincipal = $especialidades['principal'] ?? null;
                                                        $especialidadesSecundarias = $especialidades['secundarias'] ?? [];
                                                    @endphp
                                                    @if($especialidadPrincipal)
                                                        <div class="d-inline-block px-2 py-1 rounded-pill bg-primary-light text-primary mr-1 mb-1 font-weight-medium">
                                                            {{ $especialidadPrincipal }}
                                                        </div>
                                                    @endif
                                                    @if(count($especialidadesSecundarias) > 0)
                                                        @foreach(array_slice($especialidadesSecundarias, 0, 2) as $especialidad)
                                                            <div class="d-inline-block px-2 py-1 rounded-pill bg-secondary-light text-secondary mr-1 mb-1 font-weight-medium">{{ $especialidad }}</div>
                                                        @endforeach
                                                        @if(count($especialidadesSecundarias) > 2)
                                                            <div class="d-inline-block px-2 py-1 rounded-pill bg-light text-muted mr-1 mb-1 font-weight-medium">+{{ count($especialidadesSecundarias) - 2 }}</div>
                                                        @endif
                                                    @endif
                                                    @if(!$especialidadPrincipal && count($especialidadesSecundarias) === 0)
                                                        <span class="text-muted">Sin especialidades</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $instructor->status ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $instructor->status ? 'Activo' : 'Inactivo' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('VER INSTRUCTOR')
                                                            <a href="{{ route('instructor.show', $instructor->id) }}" 
                                                               class="btn btn-light btn-sm" 
                                                               data-toggle="tooltip" 
                                                               title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR INSTRUCTOR')
                                                            <a href="{{ route('instructor.edit', $instructor->id) }}" 
                                                               class="btn btn-light btn-sm" 
                                                               data-toggle="tooltip" 
                                                               title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('GESTIONAR ESPECIALIDADES INSTRUCTOR')
                                                            <a href="{{ route('instructor.gestionarEspecialidades', $instructor->id) }}" 
                                                               class="btn btn-light btn-sm" 
                                                               data-toggle="tooltip" 
                                                               title="Gestionar especialidades">
                                                                <i class="fas fa-graduation-cap text-primary"></i>
                                                            </a>
                                                        @endcan
                                                        @can('VER FICHAS ASIGNADAS')
                                                            <a href="{{ route('instructor.fichasAsignadas', $instructor->id) }}" 
                                                               class="btn btn-light btn-sm" 
                                                               data-toggle="tooltip" 
                                                               title="Ver fichas asignadas">
                                                                <i class="fas fa-clipboard-list text-success"></i>
                                                            </a>
                                                        @endcan
                                                        @can('ELIMINAR INSTRUCTOR')
                                                            <form action="{{ route('instructor.destroy', $instructor->id) }}" 
                                                                  method="POST" class="d-inline formulario-eliminar">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light btn-sm" 
                                                                        data-toggle="tooltip" 
                                                                        title="Eliminar">
                                                                    <i class="fas fa-trash text-danger"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data"
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay instructores registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="float-right">
                                {{ $instructores->links() }}
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
    <script>
        $(document).ready(function() {
            // Confirmación para formularios de eliminación
            $('.formulario-eliminar').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                
                Swal.fire({
                    title: '¿Eliminar Instructor?',
                    text: 'Esta acción eliminará el instructor pero mantendrá la persona intacta. ¿Está seguro?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Tooltips para elementos interactivos
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection