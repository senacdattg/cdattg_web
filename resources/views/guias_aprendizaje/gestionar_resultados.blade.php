@extends('adminlte::page')

@section('title', 'Gestionar Resultados - ' . $guiaAprendizaje->nombre)

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-tasks"></i>
                Gestionar Resultados de Aprendizaje
            </h1>
            <p class="text-muted">{{ $guiaAprendizaje->nombre }}</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('guias-aprendizaje.show', $guiaAprendizaje) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la Guía
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Alertas -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Resultados Asignados -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">
                            <i class="fas fa-check-circle"></i>
                            Resultados Asignados
                            <span class="badge badge-light ml-2">{{ $resultadosAsignados->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($resultadosAsignados->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Tipo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($resultadosAsignados as $resultado)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-primary">{{ $resultado->codigo }}</span>
                                                </td>
                                                <td>{{ $resultado->nombre }}</td>
                                                <td>
                                                    @if($resultado->pivot->es_obligatorio)
                                                        <span class="badge badge-danger">Obligatorio</span>
                                                    @else
                                                        <span class="badge badge-warning">Opcional</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <!-- Cambiar obligatoriedad -->
                                                        <form method="POST" action="{{ route('guias-aprendizaje.cambiarObligatoriedad', [$guiaAprendizaje, $resultado]) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="es_obligatorio" value="{{ $resultado->pivot->es_obligatorio ? 0 : 1 }}">
                                                            <button type="submit" class="btn btn-sm {{ $resultado->pivot->es_obligatorio ? 'btn-warning' : 'btn-danger' }}" 
                                                                    title="{{ $resultado->pivot->es_obligatorio ? 'Marcar como Opcional' : 'Marcar como Obligatorio' }}">
                                                                <i class="fas {{ $resultado->pivot->es_obligatorio ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <!-- Desasociar -->
                                                        <form method="POST" action="{{ route('guias-aprendizaje.desasociarResultado', [$guiaAprendizaje, $resultado]) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('¿Está seguro de desasociar este resultado?')"
                                                                    title="Desasociar Resultado">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                <p>No hay resultados asignados a esta guía.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Resultados Disponibles -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle"></i>
                            Resultados Disponibles
                            <span class="badge badge-light ml-2">{{ $resultadosDisponibles->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($resultadosDisponibles->count() > 0)
                            @foreach($resultadosPorCompetencia as $competencia => $resultados)
                                <div class="mb-4">
                                    <h5 class="text-primary">
                                        <i class="fas fa-graduation-cap"></i>
                                        {{ $competencia }}
                                        <span class="badge badge-secondary">{{ $resultados->count() }}</span>
                                    </h5>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Duración</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($resultados as $resultado)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-info">{{ $resultado->codigo }}</span>
                                                        </td>
                                                        <td>{{ Str::limit($resultado->nombre, 30) }}</td>
                                                        <td>{{ $resultado->duracion }}h</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-success" 
                                                                    data-toggle="modal" 
                                                                    data-target="#asociarModal{{ $resultado->id }}"
                                                                    title="Asociar Resultado">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <p>Todos los resultados están asignados a esta guía.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales para Asociar Resultados -->
    @foreach($resultadosDisponibles as $resultado)
        <div class="modal fade" id="asociarModal{{ $resultado->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle"></i>
                            Asociar Resultado
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('guias-aprendizaje.asociarResultado', $guiaAprendizaje) }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="resultado_id" value="{{ $resultado->id }}">
                            
                            <div class="form-group">
                                <label><strong>Código:</strong></label>
                                <p class="form-control-plaintext">{{ $resultado->codigo }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label><strong>Nombre:</strong></label>
                                <p class="form-control-plaintext">{{ $resultado->nombre }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label><strong>Duración:</strong></label>
                                <p class="form-control-plaintext">{{ $resultado->duracion }} horas</p>
                            </div>
                            
                            <div class="form-group">
                                <label for="es_obligatorio">Tipo de Resultado:</label>
                                <select name="es_obligatorio" id="es_obligatorio" class="form-control">
                                    <option value="1">Obligatorio</option>
                                    <option value="0">Opcional</option>
                                </select>
                                <small class="form-text text-muted">
                                    Los resultados obligatorios son requeridos para completar la guía.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Asociar Resultado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@stop

@section('css')
    <style>
        .card-header h3 {
            margin: 0;
        }
        .table th {
            border-top: none;
        }
        .badge {
            font-size: 0.8em;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
        .modal-body .form-control-plaintext {
            background-color: #f8f9fa;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Confirmación para desasociar
            $('form[action*="desasociarResultado"]').on('submit', function(e) {
                if (!confirm('¿Está seguro de desasociar este resultado de la guía?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@stop
