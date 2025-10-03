<form method="POST" action="{{ route('programa.store') }}" class="row g-3">
    @csrf
    <div class="col-md-6">
        <div class="form-group">
            <label class="form-label fw-bold">Código del Programa</label>
            <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control @error('codigo') is-invalid @enderror" 
                   placeholder="Ingrese el código (6 dígitos)" maxlength="6" required>
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="form-label fw-bold">Nombre del Programa</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" 
                   placeholder="Ingrese el nombre del programa" required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="form-label fw-bold">Red de Conocimiento</label>
            <select name="red_conocimiento_id" class="form-control @error('red_conocimiento_id') is-invalid @enderror" required>
                <option value="">Seleccione una red de conocimiento</option>
                @foreach(\App\Models\RedConocimiento::all() as $red)
                    <option value="{{ $red->id }}" {{ old('red_conocimiento_id') == $red->id ? 'selected' : '' }}>
                        {{ $red->nombre }}
                    </option>
                @endforeach
            </select>
            @error('red_conocimiento_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="form-label fw-bold">Nivel de Formación</label>
            <select name="nivel_formacion_id" class="form-control @error('nivel_formacion_id') is-invalid @enderror" required>
                <option value="">Seleccione un nivel de formación</option>
                @foreach(\App\Models\Parametro::whereIn('name', ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'])->get() as $nivel)
                    <option value="{{ $nivel->id }}" {{ old('nivel_formacion_id') == $nivel->id ? 'selected' : '' }}>
                        {{ $nivel->name }}
                    </option>
                @endforeach
            </select>
            @error('nivel_formacion_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Guardar Programa
        </button>
    </div>
</form>