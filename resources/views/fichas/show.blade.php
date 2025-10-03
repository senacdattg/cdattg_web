@extends('adminlte::page')

@section('title', 'Detalles de Ficha de Caracterización')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-eye"></i> Detalles de Ficha de Caracterización</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('fichaCaracterizacion.index') }}">Fichas de Caracterización</a>
                </li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt"></i> Información General
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $ficha->status ? 'success' : 'danger' }} badge-lg">
                            {{ $ficha->obtenerEstadoTexto() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong><i class="fas fa-hashtag"></i> Número de Ficha:</strong></td>
                                    <td><span class="badge badge-primary badge-lg">{{ $ficha->ficha }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong><i class="fas fa-graduation-cap"></i> Programa:</strong></td>
                                    <td>{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong><i class="fas fa-building"></i> Sede:</strong></td>
                                    <td>{{ $ficha->sede->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong><i class="fas fa-chalkboard-teacher"></i> Instructor:</strong></td>
                                    <td>
                                        @if($ficha->instructor)
                                            {{ $ficha->instructor->persona->primer_nombre }} {{ $ficha->instructor->persona->primer_apellido }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong><i class="fas fa-laptop"></i> Modalidad:</strong></td>
                                    <td>{{ $ficha->modalidadFormacion->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong><i class="fas fa-clock"></i> Jornada:</strong></td>
                                    <td>{{ $ficha->jornadaFormacion->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong><i class="fas fa-door-open"></i> Ambiente:</strong></td>
                                    <td>
                                        @if($ficha->ambiente)
                                            {{ $ficha->ambiente->nombre }} - {{ $ficha->ambiente->piso->nombre }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><i class="fas fa-hourglass-half"></i> Total Horas:</strong></td>
                                    <td>{{ $ficha->total_horas ?? 0 }} horas</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructores Asignados -->
            @if($ficha->instructorFicha->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chalkboard-teacher"></i> Instructores Asignados
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($ficha->instructorFicha as $asignacion)
                                <div class="col-md-6 mb-3">
                                    <div class="card {{ $ficha->instructor_id == $asignacion->instructor_id ? 'border-primary' : 'border-secondary' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="mb-1">
                                                        <i class="fas fa-user"></i>
                                                        {{ $asignacion->instructor->persona->primer_nombre }} 
                                                        {{ $asignacion->instructor->persona->primer_apellido }}
                                                        @if($ficha->instructor_id == $asignacion->instructor_id)
                                                            <span class="badge badge-primary ml-2">Principal</span>
                                                        @else
                                                            <span class="badge badge-secondary ml-2">Auxiliar</span>
                                                        @endif
                                                    </h5>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-calendar"></i>
                                                        {{ $asignacion->fecha_inicio->format('d/m/Y') }} - 
                                                        {{ $asignacion->fecha_fin->format('d/m/Y') }}
                                                    </p>
                                                    <p class="text-muted mb-0">
                                                        <i class="fas fa-clock"></i>
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
                                   class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Gestionar Instructores
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chalkboard-teacher"></i> Instructores Asignados
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            No hay instructores asignados a esta ficha.
                        </div>
                        
                        @can('GESTIONAR INSTRUCTORES FICHA')
                            <a href="{{ route('fichaCaracterizacion.gestionarInstructores', $ficha->id) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Asignar Instructores
                            </a>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Fechas y Duración -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Cronograma
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-calendar-plus"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fecha de Inicio</span>
                                    <span class="info-box-number">
                                        {{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('d/m/Y') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-calendar-minus"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fecha de Fin</span>
                                    <span class="info-box-number">
                                        {{ $ficha->fecha_fin ? $ficha->fecha_fin->format('d/m/Y') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Duración</span>
                                    <span class="info-box-number">{{ $ficha->duracionEnDias() }} días</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($ficha->estaEnCurso())
                        <div class="progress mb-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                                 role="progressbar" style="width: {{ $ficha->porcentajeAvance() }}%">
                                {{ $ficha->porcentajeAvance() }}%
                            </div>
                        </div>
                        <small class="text-muted">Progreso del programa</small>
                    @endif
                </div>
            </div>

            <!-- Días de Formación -->
            @if($ficha->diasFormacion->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-week"></i> Días de Formación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($ficha->diasFormacion as $diaFormacion)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <h5 class="mb-2">
                                                <i class="fas fa-calendar-day"></i>
                                                {{ $diaFormacion->dia->name }}
                                            </h5>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-clock"></i>
                                                {{ $diaFormacion->hora_inicio }} - {{ $diaFormacion->hora_fin }}
                                            </p>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-hourglass-half"></i>
                                                {{ \Carbon\Carbon::createFromFormat('H:i', $diaFormacion->hora_inicio)->diffInHours(\Carbon\Carbon::createFromFormat('H:i', $diaFormacion->hora_fin)) }} horas/día
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @can('GESTIONAR DIAS FICHA')
                            <div class="text-center mt-3">
                                <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" 
                                   class="btn btn-info">
                                    <i class="fas fa-edit"></i> Gestionar Días de Formación
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-week"></i> Días de Formación
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            No hay días de formación asignados a esta ficha.
                        </div>
                        
                        @can('GESTIONAR DIAS FICHA')
                            <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" 
                               class="btn btn-info">
                                <i class="fas fa-plus"></i> Configurar Días de Formación
                            </a>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Aprendices -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Aprendices Asignados
                    </h3>
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
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Acciones -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Acciones
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('EDITAR PROGRAMA DE CARACTERIZACION')
                            <a href="{{ route('fichaCaracterizacion.edit', $ficha->id) }}" 
                               class="btn btn-warning btn-block">
                                <i class="fas fa-edit"></i> Editar Ficha
                            </a>
                        @endcan

                        @can('GESTIONAR INSTRUCTORES FICHA')
                            <a href="{{ route('fichaCaracterizacion.gestionarInstructores', $ficha->id) }}" 
                               class="btn btn-primary btn-block">
                                <i class="fas fa-chalkboard-teacher"></i> Gestionar Instructores
                            </a>
                        @endcan

                        @can('GESTIONAR DIAS FICHA')
                            <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" 
                               class="btn btn-info btn-block">
                                <i class="fas fa-calendar-week"></i> Gestionar Días de Formación
                            </a>
                        @endcan

                        @can('VER PROGRAMA DE CARACTERIZACION')
                            <a href="{{ route('fichaCaracterizacion.index') }}" 
                               class="btn btn-info btn-block">
                                <i class="fas fa-list"></i> Ver Todas las Fichas
                            </a>
                        @endcan

                        @can('CREAR PROGRAMA DE CARACTERIZACION')
                            <a href="{{ route('fichaCaracterizacion.create') }}" 
                               class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i> Nueva Ficha
                            </a>
                        @endcan

                        @can('ELIMINAR PROGRAMA DE CARACTERIZACION')
                            <button type="button" class="btn btn-danger btn-block" 
                                    onclick="confirmarEliminacion('{{ $ficha->ficha }}', '{{ route('fichaCaracterizacion.destroy', $ficha->id) }}')">
                                <i class="fas fa-trash"></i> Eliminar Ficha
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Estadísticas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success">
                                    <i class="fas fa-users"></i>
                                </span>
                                <h5 class="description-header">{{ $ficha->contarAprendices() }}</h5>
                                <span class="description-text">Aprendices</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <span class="description-percentage text-warning">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <h5 class="description-header">{{ $ficha->duracionEnMeses() }}</h5>
                                <span class="description-text">Meses</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($ficha->total_horas)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="description-block">
                                    <span class="description-percentage text-info">
                                        <i class="fas fa-hourglass-half"></i>
                                    </span>
                                    <h5 class="description-header">{{ $ficha->horasPromedioPorDia() }}</h5>
                                    <span class="description-text">Horas promedio/día</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información de Auditoría -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Información de Auditoría
                    </h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-calendar-plus text-success"></i>
                            <strong>Creada:</strong><br>
                            {{ $ficha->created_at->format('d/m/Y H:i') }}
                        </li>
                        
                        @if($ficha->usuarioCreacion)
                            <li>
                                <i class="fas fa-user-plus text-info"></i>
                                <strong>Por:</strong><br>
                                {{ $ficha->usuarioCreacion->name }}
                            </li>
                        @endif

                        @if($ficha->updated_at != $ficha->created_at)
                            <li>
                                <i class="fas fa-calendar-edit text-warning"></i>
                                <strong>Última modificación:</strong><br>
                                {{ $ficha->updated_at->format('d/m/Y H:i') }}
                            </li>
                            
                            @if($ficha->usuarioEdicion)
                                <li>
                                    <i class="fas fa-user-edit text-primary"></i>
                                    <strong>Modificada por:</strong><br>
                                    {{ $ficha->usuarioEdicion->name }}
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la ficha "${nombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario para enviar la petición DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
