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
                        <i class="fas fa-eye text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Detalles de Ficha de Caracterización</h1>
                        <p class="text-muted mb-0 font-weight-light">Información detallada de la ficha</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('fichaCaracterizacion.index') }}" class="link_right_header">
                                    <i class="fas fa-file-alt"></i> Fichas de Caracterización
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-eye"></i> Detalles
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
            <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('fichaCaracterizacion.index') }}">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>

            <div class="row">
                <div class="col-12">
                    <!-- Información Principal -->
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-file-alt mr-2"></i> Información General de la Ficha
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Número de Ficha</th>
                                            <td class="py-3">
                                                <span class="badge badge-primary badge-lg">{{ $ficha->ficha }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Programa de Formación</th>
                                            <td class="py-3">{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Sede</th>
                                            <td class="py-3">{{ $ficha->sede->sede ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Instructor Líder</th>
                                            <td class="py-3">
                                                @if($ficha->instructor)
                                                    <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                    {{ $ficha->instructor->persona->primer_nombre }} {{ $ficha->instructor->persona->primer_apellido }}
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Modalidad</th>
                                            <td class="py-3">{{ $ficha->modalidadFormacion->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Jornada</th>
                                            <td class="py-3">{{ $ficha->jornadaFormacion->jornada ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Ambiente</th>
                                            <td class="py-3">
                                                @if($ficha->ambiente)
                                                    <i class="fas fa-door-open mr-1"></i>
                                                    {{ $ficha->ambiente->title }} - {{ $ficha->ambiente->piso->nombre }}
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Horas</th>
                                            <td class="py-3">
                                                <i class="fas fa-hourglass-half mr-1"></i>
                                                {{ $ficha->total_horas ?? 0 }} horas
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $ficha->status ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $ficha->status ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Inicio</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $ficha->fecha_inicio ? \Carbon\Carbon::parse($ficha->fecha_inicio)->format('d/m/Y') : 'No definida' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Fin</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $ficha->fecha_fin ? \Carbon\Carbon::parse($ficha->fecha_fin)->format('d/m/Y') : 'No definida' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Instructores Asignados -->
                    @if($ficha->instructorFicha->count() > 0)
                        <div class="card detail-card no-hover">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chalkboard-teacher mr-2"></i> Instructores Asignados
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($ficha->instructorFicha as $asignacion)
                                        <div class="col-md-6 mb-3">
                                            <div class="card {{ $ficha->instructor_id == $asignacion->instructor_id ? 'border-primary' : 'border-secondary' }}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <i class="fas fa-user mr-1"></i>
                                                                {{ $asignacion->instructor->persona->primer_nombre }} 
                                                                {{ $asignacion->instructor->persona->primer_apellido }}
                                                                @if($ficha->instructor_id == $asignacion->instructor_id)
                                                                    <span class="badge badge-primary ml-2">Principal</span>
                                                                @else
                                                                    <span class="badge badge-secondary ml-2">Auxiliar</span>
                                                                @endif
                                                            </h6>
                                                            <p class="text-muted mb-1">
                                                                <i class="fas fa-calendar mr-1"></i>
                                                                {{ \Carbon\Carbon::parse($asignacion->fecha_inicio)->format('d/m/Y') }} - 
                                                                {{ \Carbon\Carbon::parse($asignacion->fecha_fin)->format('d/m/Y') }}
                                                            </p>
                                                            <p class="text-muted mb-0">
                                                                <i class="fas fa-clock mr-1"></i>
                                                                {{ $asignacion->total_horas_ficha }} horas
                                                            </p>
                                                        </div>
                                                        <div>
                                                            @if($ficha->instructor_id == $asignacion->instructor_id)
                                                                <span class="badge badge-primary">
                                                                    <i class="fas fa-star"></i> Principal
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @can('GESTIONAR INSTRUCTORES FICHA')
                                    <div class="text-center mt-3">
                                        <a href="{{ route('fichaCaracterizacion.gestionarInstructores', $ficha->id) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit mr-1"></i> Gestionar Instructores
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    @else
                        <div class="card detail-card no-hover">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chalkboard-teacher mr-2"></i> Instructores Asignados
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    No hay instructores asignados a esta ficha.
                                </div>
                                
                                @can('GESTIONAR INSTRUCTORES FICHA')
                                    <a href="{{ route('fichaCaracterizacion.gestionarInstructores', $ficha->id) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-plus mr-1"></i> Asignar Instructores
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endif

                    <!-- Días de Formación -->
                    @if($ficha->diasFormacion->count() > 0)
                        <div class="card detail-card no-hover">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-week mr-2"></i> Días de Formación
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($ficha->diasFormacion as $diaFormacion)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body text-center">
                                                    <h6 class="mb-2">
                                                        <i class="fas fa-calendar-day mr-1"></i>
                                                        {{ $diaFormacion->dia->name }}
                                                    </h6>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $diaFormacion->hora_inicio }} - {{ $diaFormacion->hora_fin }}
                                                    </p>
                                                    <p class="text-muted mb-0">
                                                        <i class="fas fa-hourglass-half mr-1"></i>
                                                        {{ $diaFormacion->calcularHorasDia() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @can('GESTIONAR DIAS FICHA')
                                    <div class="text-center mt-3">
                                        <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-edit mr-1"></i> Gestionar Días de Formación
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    @else
                        <div class="card detail-card no-hover">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-week mr-2"></i> Días de Formación
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    No hay días de formación asignados a esta ficha.
                                </div>
                                
                                @can('GESTIONAR DIAS FICHA')
                                    <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-plus mr-1"></i> Configurar Días de Formación
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endif


                    <!-- Aprendices Asignados -->
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-users mr-2"></i> Aprendices Asignados
                            </h5>
                            <div class="card-tools">
                                <span class="badge badge-info">{{ $ficha->contarAprendices() }} aprendices</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($ficha->tieneAprendices())
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Documento</th>
                                                <th>Nombre Completo</th>
                                                <th>Email</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ficha->aprendices as $aprendiz)
                                                <tr>
                                                    <td>{{ $aprendiz->persona->numero_documento }}</td>
                                                    <td>
                                                        {{ $aprendiz->persona->primer_nombre }} {{ $aprendiz->persona->primer_apellido }}
                                                        @if($aprendiz->persona->segundo_nombre)
                                                            {{ $aprendiz->persona->segundo_nombre }}
                                                        @endif
                                                        @if($aprendiz->persona->segundo_apellido)
                                                            {{ $aprendiz->persona->segundo_apellido }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $aprendiz->persona->email ?? 'N/A' }}</td>
                                                    <td>{{ $aprendiz->persona->telefono ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($aprendiz->estado)
                                                            <span class="badge badge-success">Activo</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No hay aprendices asignados a esta ficha.
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR FICHA CARACTERIZACION')
                                    <a href="{{ route('fichaCaracterizacion.edit', $ficha->id) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                
                                @can('GESTIONAR INSTRUCTORES FICHA')
                                    <a href="{{ route('fichaCaracterizacion.gestionarInstructores', $ficha->id) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i> Gestionar Instructores
                                    </a>
                                @endcan
                                
                                @can('GESTIONAR DIAS FICHA')
                                    <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" 
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-calendar-week mr-1"></i> Gestionar Días
                                    </a>
                                @endcan
                                
                                @can('GESTIONAR APRENDICES FICHA')
                                    <a href="{{ route('fichaCaracterizacion.gestionarAprendices', $ficha->id) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-users mr-1"></i> Gestionar Aprendices
                                    </a>
                                @endcan
                                
                                @can('ELIMINAR FICHA CARACTERIZACION')
                                    <form action="{{ route('fichaCaracterizacion.destroy', $ficha->id) }}" 
                                          method="POST" class="d-inline formulario-eliminar">
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

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar SweetAlert para formularios de eliminación
        document.querySelectorAll('.formulario-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas eliminar esta ficha? Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
