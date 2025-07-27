<form method="POST" action="{{ route('registro-actividades.store', $caracterizacion) }}" class="row g-2">
    @csrf
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Nombre de la actividad</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" placeholder="Ingrese el nombre">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Fecha de la actividad</label>
            <input type="date" name="fecha_evidencia" value="{{ old('fecha_evidencia') }}" class="form-control" placeholder="Seleccione la fecha" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
        </div>
    </div>

    <div class="col-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Guardar Actividad
        </button>
    </div>
</form>
