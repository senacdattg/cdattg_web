<div class="row">
    <div class="col-12">
        <form method="POST" action="{{ route('programa.store') }}" class="needs-validation" novalidate>
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Código del Programa</label>
                        <input 
                            type="number" 
                            name="codigo" 
                            value="{{ old('codigo') }}" 
                            class="form-control @error('codigo') is-invalid @enderror" 
                            placeholder="Ingrese el código (6 dígitos)" 
                            maxlength="6" 
                            required
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            min="0"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6)"
                        >
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Nombre del Programa</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" 
                               placeholder="Ingrese el nombre del programa" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
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
                    <div class="form-group mb-3">
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
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Horas totales del programa</label>
                        <input type="number" name="horas_totales" value="{{ old('horas_totales') }}"
                               class="form-control @error('horas_totales') is-invalid @enderror"
                               placeholder="Total de horas" min="1" required>
                        @error('horas_totales')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Horas etapa lectiva</label>
                        <input type="number" name="horas_etapa_lectiva" value="{{ old('horas_etapa_lectiva') }}"
                               class="form-control @error('horas_etapa_lectiva') is-invalid @enderror"
                               placeholder="Horas lectiva" min="1" required>
                        @error('horas_etapa_lectiva')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Horas etapa productiva</label>
                        <input type="number" name="horas_etapa_productiva" value="{{ old('horas_etapa_productiva') }}"
                               class="form-control @error('horas_etapa_productiva') is-invalid @enderror"
                               placeholder="Horas productiva" min="1" required>
                        @error('horas_etapa_productiva')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        Las competencias se podrán asociar o quitar desde la edición del programa una vez creado.
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Programa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
