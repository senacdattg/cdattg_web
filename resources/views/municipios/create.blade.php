<form method="POST" action="{{ route('municipio.store') }}" class="row g-3">
    @csrf
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Nombre del Municipio</label>
            <input type="text" name="municipio" value="{{ old('municipio') }}" class="form-control" placeholder="Ingrese el nombre">
            <input type="hidden" name="departamento_id" value="{{ Auth::user()->persona->departamento_id }}">
        </div>
    </div>
    <div class="col-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Guardar Municipio
        </button>
    </div>
</form>