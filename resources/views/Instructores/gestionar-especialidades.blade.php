@extends('adminlte::page')

@section('title', 'Gestionar Especialidades - ' . $instructor->nombre_completo)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-chalkboard-teacher mr-2"></i>
                Gestionar Especialidades
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-custom float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('home.index') }}">
                        <i class="fas fa-home mr-1"></i>Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('instructor.index') }}">
                        <i class="fas fa-chalkboard-teacher mr-1"></i>Instructores
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('instructor.show', $instructor->id) }}">
                        {{ $instructor->nombre_completo }}
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-cogs mr-1"></i>Especialidades
                </li>
            </ol>
        </div>
    </div>
@stop

@section('css')
    <style>
        .specialty-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .specialty-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .primary-specialty {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .secondary-specialty {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .available-specialty {
            background: white;
            border: 2px solid #e9ecef;
        }
        
        .specialty-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .btn-specialty {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary-specialty {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .btn-primary-specialty:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-1px);
        }
        
        .btn-secondary-specialty {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .btn-secondary-specialty:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-1px);
        }
        
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Información del Instructor -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card specialty-card available-specialty">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <i class="fas fa-user-circle specialty-icon text-primary"></i>
                            </div>
                            <div class="col-md-8">
                                <h4 class="mb-1">{{ $instructor->nombre_completo }}</h4>
                                <p class="mb-1"><strong>Documento:</strong> {{ $instructor->numero_documento }}</p>
                                <p class="mb-0"><strong>Regional:</strong> {{ $instructor->regional->nombre ?? 'Sin asignar' }}</p>
                            </div>
                            <div class="col-md-2 text-right">
                                <a href="{{ route('instructor.show', $instructor->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye mr-1"></i>Ver Perfil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Especialidad Principal -->
            <div class="col-md-6">
                <div class="card specialty-card">
                    <div class="card-header primary-specialty">
                        <h5 class="mb-0">
                            <i class="fas fa-star mr-2"></i>
                            Especialidad Principal
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($especialidadPrincipal)
                            <div class="text-center">
                                <i class="fas fa-medal specialty-icon"></i>
                                <h5>{{ $especialidadPrincipal }}</h5>
                                <form method="POST" action="{{ route('instructor.removerEspecialidad', $instructor->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="especialidad" value="{{ $especialidadPrincipal }}">
                                    <input type="hidden" name="tipo" value="principal">
                                    <button type="submit" class="btn btn-danger btn-sm btn-specialty" 
                                            onclick="return confirm('¿Está seguro de remover esta especialidad principal?')">
                                        <i class="fas fa-times mr-1"></i>Remover
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-plus-circle"></i>
                                <h6>Sin especialidad principal</h6>
                                <p class="mb-3">Asigne una especialidad principal al instructor</p>
                                
                                <!-- Formulario para asignar especialidad principal -->
                                <form method="POST" action="{{ route('instructor.asignarEspecialidad', $instructor->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="tipo" value="principal">
                                    <div class="form-group mb-3">
                                        <select name="red_conocimiento_id" class="form-control" required>
                                            <option value="">Seleccionar red de conocimiento</option>
                                            @foreach($redesConocimiento as $red)
                                                <option value="{{ $red->id }}">{{ $red->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-specialty">
                                        <i class="fas fa-plus mr-1"></i>Asignar Principal
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Especialidades Secundarias -->
            <div class="col-md-6">
                <div class="card specialty-card">
                    <div class="card-header secondary-specialty">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group mr-2"></i>
                            Especialidades Secundarias
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($especialidadesSecundarias) > 0)
                            <div class="row">
                                @foreach($especialidadesSecundarias as $especialidad)
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center p-3 rounded" 
                                             style="background: rgba(240, 147, 251, 0.1); border: 1px solid rgba(240, 147, 251, 0.3);">
                                            <span><i class="fas fa-check-circle mr-2 text-success"></i>{{ $especialidad }}</span>
                                            <form method="POST" action="{{ route('instructor.removerEspecialidad', $instructor->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="especialidad" value="{{ $especialidad }}">
                                                <input type="hidden" name="tipo" value="secundaria">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('¿Está seguro de remover esta especialidad secundaria?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-layer-group"></i>
                                <h6>Sin especialidades secundarias</h6>
                                <p class="mb-3">Puede asignar hasta 3 especialidades secundarias</p>
                            </div>
                        @endif

                        @if(count($especialidadesSecundarias) < 3)
                            <!-- Formulario para asignar especialidad secundaria -->
                            <div class="mt-3 p-3 rounded" style="background: rgba(240, 147, 251, 0.05); border: 1px dashed rgba(240, 147, 251, 0.3);">
                                <form method="POST" action="{{ route('instructor.asignarEspecialidad', $instructor->id) }}">
                                    @csrf
                                    <input type="hidden" name="tipo" value="secundaria">
                                    <div class="form-group mb-2">
                                        <select name="red_conocimiento_id" class="form-control form-control-sm" required>
                                            <option value="">Seleccionar especialidad secundaria</option>
                                            @foreach($redesConocimiento as $red)
                                                @if($red->nombre !== $especialidadPrincipal && !in_array($red->nombre, $especialidadesSecundarias))
                                                    <option value="{{ $red->id }}">{{ $red->nombre }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-secondary btn-sm btn-specialty">
                                        <i class="fas fa-plus mr-1"></i>Agregar Secundaria
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Redes de Conocimiento Disponibles -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card specialty-card available-specialty">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-network-wired mr-2"></i>
                            Redes de Conocimiento Disponibles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($redesConocimiento as $red)
                                <div class="col-md-4 mb-3">
                                    <div class="p-3 rounded" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                                        <h6 class="mb-1">{{ $red->nombre }}</h6>
                                        <small class="text-muted">
                                            @if($red->nombre === $especialidadPrincipal)
                                                <span class="badge badge-primary">Principal</span>
                                            @elseif(in_array($red->nombre, $especialidadesSecundarias))
                                                <span class="badge badge-secondary">Secundaria</span>
                                            @else
                                                <span class="badge badge-light">Disponible</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@stop
