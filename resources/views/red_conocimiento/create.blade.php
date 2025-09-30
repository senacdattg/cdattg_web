<form method="POST" action="{{ route('red-conocimiento.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="form-group">
        <div class="mb-4">
            <label for="nombre" class="form-label">Nombre de la Red de Conocimiento</label>
            <input type="text" 
                    id="nombre" 
                    name="nombre" 
                    class="form-control @error('nombre') is-invalid @enderror" 
                    value="{{ old('nombre') }}" 
                    required 
                    autofocus
                    placeholder="Ingrese el nombre de la red de conocimiento">
            @error('nombre')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="regionals_id" class="form-label">Regional</label>
            <select id="regionals_id" 
                    name="regionals_id" 
                    class="form-control @error('regionals_id') is-invalid @enderror">
                <option value="">Selecciona una regional (opcional)</option>
                @foreach ($regionales as $regional)
                    <option value="{{ $regional->id }}" {{ old('regionals_id') == $regional->id ? 'selected' : '' }}>
                        {{ $regional->nombre }}
                    </option>
                @endforeach
            </select>
            @error('regionals_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-2"></i>
                Crear Red de Conocimiento
            </button>
        </div>
    </div>
</form>
