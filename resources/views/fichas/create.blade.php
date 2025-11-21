<div class="row">
    <div class="col-12">
        <form action="{{ route('fichaCaracterizacion.store') }}" method="POST" id="formCreateFicha" class="needs-validation"
            novalidate>
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="ficha" class="form-label fw-bold">Número de Ficha <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ficha') is-invalid @enderror" id="ficha" name="ficha"
                            value="{{ old('ficha') }}" placeholder="Ej: 123456" maxlength="50" required>
                        @error('ficha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="programa_formacion_id" class="form-label fw-bold">Programa de Formación <span
                                class="text-danger">*</span></label>
                        <select class="form-control select2 @error('programa_formacion_id') is-invalid @enderror"
                            id="programa_formacion_id" name="programa_formacion_id" data-placeholder="Seleccione un programa..."
                            required>
                            <option value="">Seleccione un programa...</option>
                            @foreach($programas as $programa)
                                <option value="{{ $programa->id }}"
                                    {{ old('programa_formacion_id') == $programa->id ? 'selected' : '' }}
                                    data-sede="{{ $programa->sede_id }}">
                                    {{ $programa->nombre }} ({{ $programa->codigo }})
                                </option>
                            @endforeach
                        </select>
                        @error('programa_formacion_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="fecha_inicio" class="form-label fw-bold">Fecha de Inicio <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror"
                            id="fecha_inicio" name="fecha_inicio" 
                            value="{{ old('fecha_inicio') }}" 
                            min="{{ \Carbon\Carbon::now()->subYears(2)->format('Y-m-d') }}"
                            required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="fecha_fin" class="form-label fw-bold">Fecha de Fin <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin"
                            name="fecha_fin" value="{{ old('fecha_fin') }}" required>
                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="sede_id" class="form-label fw-bold">Sede</label>
                        <select class="form-control select2 @error('sede_id') is-invalid @enderror" id="sede_id"
                            name="sede_id" data-placeholder="Seleccione una sede...">
                            <option value="">Seleccione una sede...</option>
                            @foreach($sedes ?? [] as $sede)
                                <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                    {{ $sede->sede }}
                                </option>
                            @endforeach
                        </select>
                        @error('sede_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="instructor_id" class="form-label fw-bold">Instructor Principal</label>
                        <select class="form-control select2 @error('instructor_id') is-invalid @enderror" id="instructor_id"
                            name="instructor_id" data-placeholder="Seleccione un instructor...">
                            <option value="">Seleccione un instructor...</option>
                            @foreach($instructores ?? [] as $instructor)
                                <option value="{{ $instructor->id }}"
                                    {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                    {{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}
                                    @if($instructor->persona->segundo_nombre)
                                        {{ $instructor->persona->segundo_nombre }}
                                    @endif
                                    @if($instructor->persona->segundo_apellido)
                                        {{ $instructor->persona->segundo_apellido }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('instructor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="modalidad_formacion_id" class="form-label fw-bold">Modalidad de Formación</label>
                        <select class="form-control select2 @error('modalidad_formacion_id') is-invalid @enderror"
                            id="modalidad_formacion_id" name="modalidad_formacion_id"
                            data-placeholder="Seleccione una modalidad...">
                            <option value="">Seleccione una modalidad...</option>
                            @foreach($modalidades ?? [] as $modalidad)
                                <option value="{{ $modalidad->id }}"
                                    {{ old('modalidad_formacion_id') == $modalidad->id ? 'selected' : '' }}>
                                    {{ $modalidad->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('modalidad_formacion_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="jornada_id" class="form-label fw-bold">Jornada de Formación</label>
                        <select class="form-control select2 @error('jornada_id') is-invalid @enderror" id="jornada_id"
                            name="jornada_id" data-placeholder="Seleccione una jornada...">
                            <option value="">Seleccione una jornada...</option>
                            @foreach($jornadas ?? [] as $jornada)
                                <option value="{{ $jornada->id }}" {{ old('jornada_id') == $jornada->id ? 'selected' : '' }}>
                                    {{ $jornada->jornada }}
                                </option>
                            @endforeach
                        </select>
                        @error('jornada_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="ambiente_id" class="form-label fw-bold">Ambiente</label>
                        <select class="form-control select2 @error('ambiente_id') is-invalid @enderror" id="ambiente_id"
                            name="ambiente_id" data-placeholder="Seleccione un ambiente..." disabled>
                            <option value="">Primero seleccione una sede...</option>
                        </select>
                        @error('ambiente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="total_horas" class="form-label fw-bold">Total de Horas</label>
                        <input type="number" class="form-control @error('total_horas') is-invalid @enderror"
                            id="total_horas" name="total_horas" value="{{ old('total_horas') }}" min="1" max="9999"
                            placeholder="Ej: 120">
                        @error('total_horas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Días de Formación <span class="text-danger">*</span></label>
                        <div class="row" id="dias-formacion-container">
                            @php
                                $diasSemana = [
                                    ['id' => 12, 'nombre' => 'LUNES'],
                                    ['id' => 13, 'nombre' => 'MARTES'],
                                    ['id' => 14, 'nombre' => 'MIÉRCOLES'],
                                    ['id' => 15, 'nombre' => 'JUEVES'],
                                    ['id' => 16, 'nombre' => 'VIERNES'],
                                    ['id' => 17, 'nombre' => 'SÁBADO'],
                                    ['id' => 18, 'nombre' => 'DOMINGO'],
                                ];
                            @endphp
                            @foreach($diasSemana as $dia)
                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input dia-formacion-checkbox"
                                            id="dia_{{ $dia['id'] }}" name="dias_formacion[]" value="{{ $dia['id'] }}"
                                            {{ in_array($dia['id'], old('dias_formacion', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="dia_{{ $dia['id'] }}">
                                            {{ $dia['nombre'] }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row mt-3" id="horarios-container" style="display: none;">
                            <div class="col-md-12">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-clock mr-2"></i>Horarios por Día</h6>
                                <div id="horarios-dias" class="row">
                                </div>
                            </div>
                        </div>
                        @error('dias_formacion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" value="1"
                                {{ old('status', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label fw-bold" for="status">
                                Ficha Activa
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Crear Ficha
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
