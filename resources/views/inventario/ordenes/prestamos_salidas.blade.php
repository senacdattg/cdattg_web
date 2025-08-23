@extends('adminlte::page')

@vite([
    'resources/css/inventario/shared/base.css',
    'resources/css/inventario/ordenes.css'
])

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-exchange-alt mr-2"></i>
                Préstamo o Salida
            </h3>
        </div>
        
        <div class="card-body">
            <form action="{{ route('inventario.ordenes.prestamos_salidas.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- Nombre -->
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-user text-primary"></i>
                            Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Documento -->
                    <div class="col-md-6 mb-3">
                        <label for="documento" class="form-label">
                            <i class="fas fa-id-card text-primary"></i>
                            Documento <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('documento') is-invalid @enderror" 
                                id="documento" name="documento" value="{{ old('documento') }}" required>
                        @error('documento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Rol -->
                    <div class="col-md-6 mb-3">
                        <label for="rol" class="form-label">
                            <i class="fas fa-user-tag text-primary"></i>
                            Rol <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('rol') is-invalid @enderror" 
                                id="rol" name="rol" required>
                            <option value="">Seleccionar rol...</option>
                            <option value="estudiante" {{ old('rol') == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                            <option value="instructor" {{ old('rol') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="coordinador" {{ old('rol') == 'coordinador' ? 'selected' : '' }}>Coordinador</option>
                            <option value="administrativo" {{ old('rol') == 'administrativo' ? 'selected' : '' }}>Administrativo</option>
                        </select>
                        @error('rol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nombre del programa formación -->
                    <div class="col-md-6 mb-3">
                        <label for="programa_formacion" class="form-label">
                            <i class="fas fa-graduation-cap text-primary"></i>
                            Nombre del programa formación <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('programa_formacion') is-invalid @enderror" 
                                id="programa_formacion" name="programa_formacion" value="{{ old('programa_formacion') }}" required>
                        @error('programa_formacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Ficha -->
                    <div class="col-md-6 mb-3">
                        <label for="ficha" class="form-label">
                            <i class="fas fa-ticket-alt text-primary"></i>
                            Ficha <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('ficha') is-invalid @enderror" 
                                id="ficha" name="ficha" value="{{ old('ficha') }}" required>
                        @error('ficha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo -->
                    <div class="col-md-6 mb-3">
                        <label for="tipo" class="form-label">
                            <i class="fas fa-tags text-primary"></i>
                            Tipo <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('tipo') is-invalid @enderror" 
                                id="tipo" name="tipo" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="prestamo" {{ old('tipo') == 'prestamo' ? 'selected' : '' }}>Préstamo</option>
                            <option value="salida" {{ old('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Fecha adquirido -->
                    <div class="col-md-6 mb-3">
                        <label for="fecha_adquirido" class="form-label">
                            <i class="fas fa-calendar-plus text-primary"></i>
                            Fecha de adquisición <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control @error('fecha_adquirido') is-invalid @enderror" 
                                id="fecha_adquirido" name="fecha_adquirido" value="{{ old('fecha_adquirido') }}" required>
                        @error('fecha_adquirido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fecha devolución -->
                    <div class="col-md-6 mb-3">
                        <label for="fecha_devolucion" class="form-label">
                            <i class="fas fa-calendar-minus text-primary"></i>
                            Fecha de devolución <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control @error('fecha_devolucion') is-invalid @enderror" 
                                id="fecha_devolucion" name="fecha_devolucion" value="{{ old('fecha_devolucion') }}" required>
                        @error('fecha_devolucion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-comment-alt text-primary"></i>
                            Descripción <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                id="descripcion" name="descripcion" rows="4" 
                                placeholder="Describe el motivo del préstamo/salida, condiciones especiales, etc." required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save mr-2"></i>
                                Crear Préstamo/Salida
                            </button>
                            <a href="{{ route('inventario.ordenes.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar
                            </a>
                            <button type="reset" class="btn btn-warning ml-2">
                                <i class="fas fa-undo mr-2"></i>
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Nota informativa -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Nota:</strong> Todos los campos marcados con <span class="text-danger">*</span> son obligatorios.
                            Asegúrese de seleccionar todos los elementos antes de proceder.
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // Validación de fechas
    document.addEventListener('DOMContentLoaded', function() {
        const fechaAdquirido = document.getElementById('fecha_adquirido');
        const fechaDevolucion = document.getElementById('fecha_devolucion');
        
        fechaAdquirido.addEventListener('change', function() {
            fechaDevolucion.min = this.value;
        });
        
        fechaDevolucion.addEventListener('change', function() {
            if (fechaAdquirido.value && this.value < fechaAdquirido.value) {
                alert('La fecha de devolución no puede ser anterior a la fecha de adquisición');
                this.value = '';
            }
        });
    });
</script>
@endsection
