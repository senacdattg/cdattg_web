@extends('adminlte::page')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-chalkboard-teacher" 
        title="Instructores"
        subtitle="Gestión de instructores del sistema"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Instructores', 'active' => true, 'icon' => 'fa-chalkboard-teacher']
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @can('CREAR INSTRUCTOR')
                        <a href="{{ route('instructor.create') }}" class="text-decoration-none">
                            <div class="card shadow-sm mb-4 hover-card">
                                <div class="card-header bg-white py-3 d-flex align-items-center">
                                    <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                        <i class="fas fa-plus-circle mr-2"></i> Crear Instructor
                                    </h5>
                                </div>
                            </div>
                        </a>
                    @endcan

                    <x-data-table 
                        title="Lista de Instructores"
                        searchable="true"
                        searchAction="{{ route('instructor.index') }}"
                        searchPlaceholder="Buscar instructor..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre', 'width' => '25%'],
                            ['label' => 'Documento', 'width' => '15%'],
                            ['label' => 'Especialidades', 'width' => '20%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Acciones', 'width' => '25%', 'class' => 'text-center']
                        ]"
                        :pagination="$instructores->links()"
                    >
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
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Confirmación para formularios de eliminación
            $('.formulario-eliminar').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                
                // Obtener información del instructor desde la fila de la tabla
                const row = $(form).closest('tr');
                const nombreInstructor = row.find('td:nth-child(2)').text().trim();
                const documentoInstructor = row.find('td:nth-child(3)').text().trim();
                
                Swal.fire({
                    title: '⚠️ Eliminar Instructor',
                    html: `<div class="text-left">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información del Instructor:</strong>
                        </div>
                        <p><strong>Nombre:</strong> ${nombreInstructor}</p>
                        <p><strong>Documento:</strong> ${documentoInstructor}</p>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Importante:</strong> Esta acción eliminará el instructor pero mantendrá la persona intacta.
                        </div>
                    </div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    focusConfirm: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar loading mientras se procesa
                        Swal.fire({
                            title: 'Eliminando...',
                            text: 'Por favor espere mientras se procesa la solicitud',
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        form.submit();
                    }
                });
            });

            // Tooltips para elementos interactivos
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection