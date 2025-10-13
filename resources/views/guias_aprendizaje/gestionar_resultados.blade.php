@extends('adminlte::page')

@section('css')
    @vite(['resources/css/guias_aprendizaje.css'])
    <style>
        .dashboard-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .link_right_header {
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .link_right_header:hover {
            color: #4299e1;
        }
        .breadcrumb-item {
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .breadcrumb-item i {
            font-size: 0.8rem;
            margin-right: 0.4rem;
        }
        .breadcrumb-item a {
            color: #4a5568;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #718096;
        }
        .info-badge { background-color: #e7f1ff; color: #004085; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-book-open" 
        title="Guías de Aprendizaje"
        subtitle="Gestión de guías de aprendizaje del SENA"
        :breadcrumb="[['label' => 'Guías de Aprendizaje', 'url' => '{{ route('guias-aprendizaje.index') }}', 'icon' => 'fa-book-open'], ['label' => 'Gestionar Resultados', 'icon' => 'fa-tasks', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('guias-aprendizaje.show', $guiaAprendizaje) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a la Guía
                    </a>

                    <!-- Info de la Guía -->
                    <div class="info-badge">
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Código</small>
                                <strong>{{ $guiaAprendizaje->codigo }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Resultados actuales</small>
                                <strong>{{ $resultadosAsignados->count() }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Estado</small>
                                <strong class="{{ $guiaAprendizaje->status == 1 ? 'text-success' : 'text-danger' }}">
                                    {{ $guiaAprendizaje->status == 1 ? 'Activa' : 'Inactiva' }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Resultados Asignados -->
                    <div class="card shadow-sm mb-4 no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-check-circle mr-2"></i>Resultados de Aprendizaje Asignados
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="px-4 py-3" style="width: 15%">Código</th>
                                            <th class="px-4 py-3" style="width: 50%">Nombre</th>
                                            <th class="px-4 py-3" style="width: 20%">Obligatorio</th>
                                            <th class="px-4 py-3 text-center" style="width: 15%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($resultadosAsignados as $resultado)
                                            <tr>
                                                <td class="px-4 font-weight-medium">{{ $resultado->codigo }}</td>
                                                <td class="px-4">{{ $resultado->nombre }}</td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $resultado->pivot->es_obligatorio ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $resultado->pivot->es_obligatorio ? 'Obligatorio' : 'Opcional' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        <form method="POST" action="{{ route('guias-aprendizaje.cambiarObligatoriedad', [$guiaAprendizaje, $resultado]) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="es_obligatorio" value="{{ $resultado->pivot->es_obligatorio ? 0 : 1 }}">
                                                            <button type="submit" class="btn btn-light btn-sm" 
                                                                data-toggle="tooltip" title="{{ $resultado->pivot->es_obligatorio ? 'Marcar como Opcional' : 'Marcar como Obligatorio' }}">
                                                                <i class="fas fa-toggle-{{ $resultado->pivot->es_obligatorio ? 'on' : 'off' }} text-info"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <form method="POST" action="{{ route('guias-aprendizaje.desasociarResultado', [$guiaAprendizaje, $resultado]) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-light btn-sm" 
                                                                onclick="return confirm('¿Está seguro de desasociar este resultado?')"
                                                                data-toggle="tooltip" title="Desasociar">
                                                                <i class="fas fa-times text-danger"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data" 
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay resultados de aprendizaje asignados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Asociar Nuevo Resultado -->
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-plus-circle mr-2"></i>Asociar Nuevo Resultado de Aprendizaje
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('guias-aprendizaje.asociarResultado', $guiaAprendizaje) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="resultado_id" class="form-label font-weight-bold">Resultado de Aprendizaje</label>
                                            <select name="resultado_id" id="resultado_id" class="form-control" required>
                                                <option value="">Seleccione un resultado...</option>
                                                @foreach($resultadosDisponibles as $resultado)
                                                    <option value="{{ $resultado->id }}">
                                                        {{ $resultado->codigo }} - {{ $resultado->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="es_obligatorio" class="form-label font-weight-bold">Obligatorio</label>
                                            <select name="es_obligatorio" id="es_obligatorio" class="form-control">
                                                <option value="1">Sí</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="form-group w-100">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-plus mr-1"></i>Asociar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endsection
