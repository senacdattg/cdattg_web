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
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-edit text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Editar Competencia</h1>
                        <p class="text-muted mb-0 font-weight-light">{{ $competencia->codigo }} - {{ Str::limit($competencia->nombre, 50) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <form action="{{ route('competencias.update', $competencia) }}" method="POST" id="formEditarCompetencia">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title">
                                    <i class="fas fa-clipboard-list"></i> Información de la Competencia
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo">Código <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="codigo" 
                                                   id="codigo" 
                                                   class="form-control @error('codigo') is-invalid @enderror" 
                                                   value="{{ old('codigo', $competencia->codigo) }}" 
                                                   placeholder="Ej: 38356"
                                                   required>
                                            @error('codigo')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duracion">Duración (horas) <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   name="duracion" 
                                                   id="duracion" 
                                                   class="form-control @error('duracion') is-invalid @enderror" 
                                                   value="{{ old('duracion', $competencia->duracion) }}" 
                                                   placeholder="Ej: 144"
                                                   min="1"
                                                   step="0.01"
                                                   required>
                                            @error('duracion')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="nombre" 
                                           id="nombre" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           value="{{ old('nombre', $competencia->nombre) }}" 
                                           placeholder="Ej: IMPLANTACIÓN DEL SOFTWARE"
                                           required>
                                    @error('nombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" 
                                              id="descripcion" 
                                              class="form-control @error('descripcion') is-invalid @enderror" 
                                              rows="3"
                                              placeholder="Descripción detallada de la competencia...">{{ old('descripcion', $competencia->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <small class="text-muted">Máximo 1000 caracteres</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_inicio">Fecha de Inicio <span class="text-danger">*</span></label>
                                            <input type="date" 
                                                   name="fecha_inicio" 
                                                   id="fecha_inicio" 
                                                   class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                                   value="{{ old('fecha_inicio', $competencia->fecha_inicio ? $competencia->fecha_inicio->format('Y-m-d') : '') }}"
                                                   required>
                                            @error('fecha_inicio')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_fin">Fecha de Fin <span class="text-danger">*</span></label>
                                            <input type="date" 
                                                   name="fecha_fin" 
                                                   id="fecha_fin" 
                                                   class="form-control @error('fecha_fin') is-invalid @enderror" 
                                                   value="{{ old('fecha_fin', $competencia->fecha_fin ? $competencia->fecha_fin->format('Y-m-d') : '') }}"
                                                   required>
                                            @error('fecha_fin')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="status" 
                                               name="status" 
                                               value="1"
                                               {{ old('status', $competencia->status) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">
                                            <i class="fas fa-check-circle text-success"></i> Competencia Activa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h3 class="card-title">
                                    <i class="fas fa-cog"></i> Opciones
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-save"></i> Actualizar Competencia
                                    </button>
                                </div>
                                <div>
                                    <a href="{{ route('competencias.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header bg-secondary">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i> Información
                                </h3>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <p><strong>Creado por:</strong> {{ $competencia->userCreate->name ?? 'N/A' }}</p>
                                    <p><strong>Fecha creación:</strong> {{ $competencia->created_at ? $competencia->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                    <p><strong>Última edición:</strong> {{ $competencia->updated_at ? $competencia->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                    <p><strong>RAPs asociados:</strong> {{ $competencia->resultadosAprendizaje->count() }}</p>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
        // Validación de fechas
        $('#fecha_inicio, #fecha_fin').on('change', function() {
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();
            
            if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas inválidas',
                    text: 'La fecha de inicio debe ser anterior o igual a la fecha de fin.',
                    confirmButtonText: 'Entendido'
                });
            }
        });
    });
</script>
@endsection

