
<form method="POST" action="{{ route('regional.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="form-group">
        <div class="mb-4">
            <label for="nombre" class="form-label">Nombre de la Regional</label>
            <input type="text" 
                    id="nombre" 
                    name="nombre" 
                    class="form-control @error('nombre') is-invalid @enderror" 
                    value="{{ old('nombre') }}" 
                    required 
                    autofocus
                    placeholder="Ingrese el nombre de la regional">
            @error('nombre')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="departamento_id" class="form-label">Departamento</label>
            <select id="departamento_id" 
                    name="departamento_id" 
                    class="form-control @error('departamento_id') is-invalid @enderror" 
                    required>
                <option value="">Selecciona un departamento</option>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>
                        {{ $departamento->departamento }}
                    </option>
                @endforeach
            </select>
            @error('departamento_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-2"></i>
                Crear Regional
            </button>
        </div>
    </div>
</form>