<form method="POST" action="{{ route('registro-actividades.store', $caracterizacion) }}" class="row g-2" id="form-registro-actividad">
    @csrf
    <!-- Información del RAP actual -->
    <div class="col-md-12 mb-3">
        <div class="card border-warning shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-calendar-times mr-2"></i>
                <strong>Resultado de Aprendizaje actual</strong>
            </div>
            <div class="card-body">
                @if($rapActual)
                    <div class="row">
                        <div class="col-md-6">
                            <strong>RAP:</strong> {{ $rapActual->codigo }} - {{ $rapActual->nombre }}
                        </div>
                        <div class="col-md-3">
                            <strong>Inicio:</strong> {{ \Carbon\Carbon::parse($rapActual->fecha_inicio)->format('d/m/Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Fin:</strong> {{ \Carbon\Carbon::parse($rapActual->fecha_fin)->format('d/m/Y') }}
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle"></i> Solo podrás crear actividades hasta el {{ \Carbon\Carbon::parse($rapActual->fecha_fin)->format('d/m/Y') }}.
                    </small>
                @else
                    <div class="text-muted">
                        <i class="fas fa-exclamation-triangle"></i> No hay un RAP activo configurado para el período actual.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Nombre de la actividad</label>
            <input type="text" name="nombre" id="nombre_actividad" value="{{ old('nombre') }}" class="form-control" placeholder="Ingrese el nombre de la actividad">
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Fecha de la actividad</label>
            <input type="text" name="fecha_evidencia" id="fecha_actividad" value="{{ old('fecha_evidencia') }}" class="form-control" placeholder="Seleccione la fecha" readonly>
            <small class="form-text text-muted">
                <i class="fas fa-calendar-alt"></i> Haz clic en el campo para abrir el calendario. Solo se muestran fechas válidas para formación.
            </small>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Horas de la actividad</label>
            <input type="number" name="horas" id="horas_actividad" value="{{ old('horas') }}" class="form-control" placeholder="Ingrese las horas" min="1" max="8">
            <small class="form-text text-muted">
                <i class="fas fa-clock"></i> Ingrese el número de horas de la actividad (máximo 8 horas por día).
            </small>
        </div>
    </div>

    <!-- Información del día seleccionado -->
    <div class="col-md-12" id="info-dia-seleccionado">
        <!-- Se llenará dinámicamente -->
    </div>

    <!-- Información de actividad existente -->
    <div class="col-md-12" id="info-actividad-existente">
        <!-- Se llenará dinámicamente -->
    </div>

    <div class="col-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Guardar Actividad
        </button>
    </div>
</form>

<style>
/* Estilos para Flatpickr */
.flatpickr-input {
    cursor: pointer !important;
}

.flatpickr-input:focus {
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
}

/* Estilos para fechas con actividades */
.flatpickr-day.has-activity {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.flatpickr-day.has-activity:hover {
    background-color: #e0a800 !important;
}
</style>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
@section('js')
    @vite(['resources/js/pages/registro-actividades-form.js'])
@endsection

<script>
// Pasar datos del PHP al JavaScript
window.diasFormacion = @json($caracterizacion->instructorFichaDias->pluck('dia_id')->toArray());
window.fechaFinRap = @json($caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual()?->fecha_fin);
window.actividades = @json($actividades ?? []);
</script>
