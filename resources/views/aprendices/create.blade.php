<form method="POST" action="{{ route('aprendices.store') }}" class="row">
    @csrf

    <div class="col-md-6">
        <div class="form-group">
            <label for="persona_id" class="form-label font-weight-bold">Persona <span class="text-danger">*</span></label>
            <select name="persona_id" id="persona_id" 
                class="form-control select2 @error('persona_id') is-invalid @enderror">
                <option value="" selected disabled>Seleccione una persona</option>
                @foreach ($personas as $persona)
                    <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                        {{ $persona->nombre_completo }} - {{ $persona->numero_documento }}
                    </option>
                @endforeach
            </select>
            @error('persona_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i> Solo se muestran personas que no son aprendices
            </small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="ficha_caracterizacion_id" class="form-label font-weight-bold">Ficha de Caracterización <span class="text-danger">*</span></label>
            <select name="ficha_caracterizacion_id" id="ficha_caracterizacion_id" 
                class="form-control select2 @error('ficha_caracterizacion_id') is-invalid @enderror">
                <option value="" selected disabled>Seleccione una ficha</option>
                @foreach ($fichas as $ficha)
                    <option value="{{ $ficha->id }}" {{ old('ficha_caracterizacion_id') == $ficha->id ? 'selected' : '' }}>
                        {{ $ficha->ficha }} - {{ $ficha->programaFormacion->nombre ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
            @error('ficha_caracterizacion_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i> Ficha de caracterización a la que pertenecerá el aprendiz
            </small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="estado" class="form-label font-weight-bold">Estado <span class="text-danger">*</span></label>
            <select name="estado" class="form-control @error('estado') is-invalid @enderror" required>
                <option value="1" {{ old('estado', '1') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <hr class="mt-4">
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i>Guardar Aprendiz
            </button>
        </div>
    </div>
</form>

