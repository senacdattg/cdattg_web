<form method="POST" action="{{ route('parametro.store') }}" class="row g-3">
    @csrf
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Nombre del Parámetro</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Ingrese el nombre">
        </div>
    </div>
    <div class="col-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Guardar Parámetro
        </button>
    </div>
</form>