@props([
    'departamentos' => [],
    'municipios' => [],
    'municipioSeleccionado' => null,
    'departamentoSeleccionado' => null,
    'required' => false
])

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="departamento_id">
                Departamento 
                @if($required)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <select
                class="form-control @error('departamento_id') is-invalid @enderror"
                id="departamento_id"
                name="departamento_id"
                {{ $required ? 'required' : '' }}
            >
                <option value="">Seleccione un departamento</option>
                @foreach($departamentos as $departamento)
                    <option 
                        value="{{ $departamento->id }}" 
                        {{ old('departamento_id', $departamentoSeleccionado) == $departamento->id ? 'selected' : '' }}
                    >
                        {{ $departamento->departamento }}
                    </option>
                @endforeach
            </select>
            @error('departamento_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="municipio_id">
                Municipio 
                @if($required)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <select
                class="form-control @error('municipio_id') is-invalid @enderror"
                id="municipio_id"
                name="municipio_id"
                {{ $required ? 'required' : '' }}
            >
                <option value="">Seleccione un municipio</option>
                @foreach($municipios as $municipio)
                    <option 
                        value="{{ $municipio->id }}" 
                        {{ old('municipio_id', $municipioSeleccionado) == $municipio->id ? 'selected' : '' }}
                    >
                        {{ $municipio->municipio }} ({{ $municipio->departamento->departamento ?? '' }})
                    </option>
                @endforeach
            </select>
            @error('municipio_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
