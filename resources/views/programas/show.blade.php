@extends('adminlte::page')

@section('title', 'Detalles del Programa de Formación')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-eye"></i>
            Detalles del Programa de Formación
        </h1>
        <div>
            @can('programa.edit')
                <a href="{{ route('programa.edit', $programa->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endcan
            <a href="{{ route('programa.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Información principal -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-graduation-cap"></i> Información del Programa
                </h3>
                <div class="card-tools">
                    @if($programa->status)
                        <span class="badge badge-success badge-lg">
                            <i class="fas fa-check"></i> Activo
                        </span>
                    @else
                        <span class="badge badge-danger badge-lg">
                            <i class="fas fa-times"></i> Inactivo
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong><i class="fas fa-hashtag text-info"></i> ID:</strong></td>
                                <td><span class="badge badge-info">{{ $programa->id }}</span></td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-code text-secondary"></i> Código:</strong></td>
                                <td><span class="badge badge-secondary">{{ $programa->codigo }}</span></td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-graduation-cap text-primary"></i> Nombre:</strong></td>
                                <td>{{ $programa->nombre }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong><i class="fas fa-calendar-plus text-success"></i> Creado:</strong></td>
                                <td>{{ $programa->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-calendar-edit text-warning"></i> Actualizado:</strong></td>
                                <td>{{ $programa->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-user-plus text-info"></i> Creado por:</strong></td>
                                <td>
                                    @if($programa->userCreated)
                                        {{ $programa->userCreated->persona->nombre ?? 'Usuario' }}
                                    @else
                                        <span class="text-muted">No disponible</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Red de Conocimiento y Nivel de Formación -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-network-wired"></i> Red de Conocimiento
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($programa->redConocimiento)
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-network-wired"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Red de Conocimiento</span>
                                    <span class="info-box-number">{{ $programa->redConocimiento->nombre }}</span>
                                    @if($programa->redConocimiento->regional)
                                        <small class="text-muted">
                                            Regional: {{ $programa->redConocimiento->regional->nombre }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No se ha asignado una red de conocimiento
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-layer-group"></i> Nivel de Formación
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($programa->nivelFormacion)
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-layer-group"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nivel de Formación</span>
                                    <span class="info-box-number">{{ $programa->nivelFormacion->name }}</span>
                                    @if($programa->nivelFormacion->status)
                                        <small class="text-success">Activo</small>
                                    @else
                                        <small class="text-danger">Inactivo</small>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No se ha asignado un nivel de formación
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Fichas de Caracterización -->
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt"></i> Fichas de Caracterización
                </h3>
                <div class="card-tools">
                    <span class="badge badge-warning">{{ $programa->fichasCaracterizacion->count() }} fichas</span>
                </div>
            </div>
            <div class="card-body">
                @if($programa->fichasCaracterizacion->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ficha</th>
                                    <th>Instructor</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programa->fichasCaracterizacion as $ficha)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ $ficha->ficha }}</span>
                                        </td>
                                        <td>
                                            @if($ficha->instructor && $ficha->instructor->persona)
                                                {{ $ficha->instructor->persona->nombre }}
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('d/m/Y') : 'N/A' }}</td>
                                        <td>
                                            @if($ficha->status)
                                                <span class="badge badge-success">Activa</span>
                                            @else
                                                <span class="badge badge-danger">Inactiva</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Este programa no tiene fichas de caracterización asociadas
                    </div>
                @endif
            </div>
        </div>

        <!-- Competencias del Programa -->
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy"></i> Competencias del Programa
                </h3>
                <div class="card-tools">
                    <span class="badge badge-danger">{{ $programa->competenciasProgramas->count() }} competencias</span>
                </div>
            </div>
            <div class="card-body">
                @if($programa->competenciasProgramas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Competencia</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programa->competenciasProgramas as $competenciaPrograma)
                                    <tr>
                                        <td>
                                            @if($competenciaPrograma->competencia)
                                                {{ $competenciaPrograma->competencia->nombre }}
                                            @else
                                                <span class="text-muted">Competencia eliminada</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($competenciaPrograma->competencia)
                                                {{ $competenciaPrograma->competencia->fecha_inicio ? $competenciaPrograma->competencia->fecha_inicio->format('d/m/Y') : 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($competenciaPrograma->competencia)
                                                {{ $competenciaPrograma->competencia->fecha_fin ? $competenciaPrograma->competencia->fecha_fin->format('d/m/Y') : 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($competenciaPrograma->competencia)
                                                @php
                                                    $now = now();
                                                    $inicio = $competenciaPrograma->competencia->fecha_inicio;
                                                    $fin = $competenciaPrograma->competencia->fecha_fin;
                                                @endphp
                                                @if($inicio && $fin)
                                                    @if($now->between($inicio, $fin))
                                                        <span class="badge badge-success">En curso</span>
                                                    @elseif($now->lt($inicio))
                                                        <span class="badge badge-info">Próxima</span>
                                                    @else
                                                        <span class="badge badge-secondary">Finalizada</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-warning">Sin fechas</span>
                                                @endif
                                            @else
                                                <span class="badge badge-danger">Eliminada</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Este programa no tiene competencias asociadas
                    </div>
                @endif
            </div>
        </div>

        <!-- Acciones -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i> Acciones
                </h3>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    @can('programa.edit')
                        <a href="{{ route('programa.edit', $programa->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Programa
                        </a>
                    @endcan
                    @can('programa.delete')
                        <button type="button" 
                                class="btn btn-danger btn-delete" 
                                data-id="{{ $programa->id }}"
                                data-name="{{ $programa->nombre }}">
                            <i class="fas fa-trash"></i> Eliminar Programa
                        </button>
                    @endcan
                    <a href="{{ route('programa.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Ver Todos los Programas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el programa de formación <strong id="programaName"></strong>?</p>
                    <p class="text-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Esta acción no se puede deshacer y eliminará todas las fichas y competencias asociadas.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('name');
            
            document.getElementById('programaName').textContent = nombre;
            document.getElementById('deleteForm').action = '{{ route("programa.destroy", ":id") }}'.replace(':id', id);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endsection
