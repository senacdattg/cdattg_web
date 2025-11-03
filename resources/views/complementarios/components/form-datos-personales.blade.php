@props(['context' => 'registro', 'userData' => [], 'step' => 1, 'tiposDocumento' => [], 'generos' => []])

@if($context === 'registro')
    {{-- Versión completa sin pasos para registro --}}
    <div class="card card-success mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Información Personal Básica</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipo_documento" class="form-label">Tipo de Documento *</label>
                    <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                        <option value="">Seleccione...</option>
                        @if($tiposDocumento && $tiposDocumento->count() > 0)
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == $tipo->id ? 'selected' : '' }}>
                                    {{ ucwords(strtolower(str_replace('_', ' ', $tipo->name))) }}
                                </option>
                            @endforeach
                        @else
                            <option value="3" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '3' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="4" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '4' ? 'selected' : '' }}>Cédula de Extranjería</option>
                            <option value="5" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '5' ? 'selected' : '' }}>Pasaporte</option>
                            <option value="6" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '6' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                            <option value="7" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '7' ? 'selected' : '' }}>Registro Civil</option>
                            <option value="8" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '8' ? 'selected' : '' }}>Sin Identificación</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="numero_documento" class="form-label">Número de Documento *</label>
                    <input type="text" class="form-control" id="numero_documento" name="numero_documento"
                            value="{{ old('numero_documento', $userData['numero_documento'] ?? '') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="primer_nombre" class="form-label">Primer Nombre *</label>
                    <input type="text" class="form-control" id="primer_nombre" name="primer_nombre"
                            value="{{ old('primer_nombre', $userData['primer_nombre'] ?? '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                    <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre"
                            value="{{ old('segundo_nombre', $userData['segundo_nombre'] ?? '') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="primer_apellido" class="form-label">Primer Apellido *</label>
                    <input type="text" class="form-control" id="primer_apellido" name="primer_apellido"
                            value="{{ old('primer_apellido', $userData['primer_apellido'] ?? '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                    <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido"
                            value="{{ old('segundo_apellido', $userData['segundo_apellido'] ?? '') }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico *</label>
                <input type="email" class="form-control" id="email" name="email"
                        value="{{ old('email', $userData['email'] ?? '') }}" required>
            </div>
        </div>
    </div>

    <div class="card card-success mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-birthday-cake mr-2"></i>Información Personal Adicional</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                <input type="date" class="form-control" id="fecha_nacimiento"
                        name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $userData['fecha_nacimiento'] ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="genero" class="form-label">Género *</label>
                <select class="form-control" id="genero" name="genero" required>
                    <option value="">Seleccione...</option>
                    @if($generos && $generos->count() > 0)
                        @foreach($generos as $genero)
                            <option value="{{ $genero->id }}" {{ old('genero', $userData['genero'] ?? '') == $genero->id ? 'selected' : '' }}>
                                {{ ucwords(strtolower(str_replace('_', ' ', $genero->name))) }}
                            </option>
                        @endforeach
                    @else
                        <option value="9" {{ old('genero', $userData['genero'] ?? '') == '9' ? 'selected' : '' }}>Masculino</option>
                        <option value="10" {{ old('genero', $userData['genero'] ?? '') == '10' ? 'selected' : '' }}>Femenino</option>
                        <option value="11" {{ old('genero', $userData['genero'] ?? '') == '11' ? 'selected' : '' }}>No Define</option>
                    @endif
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono Fijo</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $userData['telefono'] ?? '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="celular" class="form-label">Celular *</label>
                    <input type="tel" class="form-control" id="celular" name="celular"
                            value="{{ old('celular', $userData['celular'] ?? '') }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-success mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Ubicación Actual</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="pais_id" class="form-label">País *</label>
                <select class="form-control" id="pais_id" name="pais_id" required>
                    <option value="">Seleccione...</option>
                    @foreach ($paises ?? [] as $pais)
                        <option value="{{ $pais->id }}" {{ old('pais_id', $userData['pais_id'] ?? '') == $pais->id ? 'selected' : '' }}>{{ $pais->pais }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="departamento_id" class="form-label">Departamento *</label>
                <select class="form-control" id="departamento_id" name="departamento_id" required>
                    <option value="">Seleccione...</option>
                    @foreach ($departamentos ?? [] as $departamento)
                        <option value="{{ $departamento->id }}" {{ old('departamento_id', $userData['departamento_id'] ?? '') == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="municipio_id" class="form-label">Municipio *</label>
                <select class="form-control" id="municipio_id" name="municipio_id" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección *</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="{{ old('direccion', $userData['direccion'] ?? '') }}" placeholder="Dirección completa" required readonly>
                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="toggleAddressForm" aria-expanded="false" aria-controls="addressForm">
                    <i class="fas fa-edit"></i> Ingresar Dirección
                </button>
            </div>
            <div id="addressForm" class="collapse mt-3" aria-labelledby="addressFormLabel">
                <div class="card card-outline-secondary">
                    <div class="card-header">
                        <h5 id="addressFormLabel" class="mb-0">Ingresar Dirección</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="carrera">Carrera *</label>
                                    <input type="text" class="form-control address-field" id="carrera"
                                        placeholder="Ej: 1" data-required="true">
                                </div>
                                <div class="form-group">
                                    <label for="calle">Calle *</label>
                                    <input type="text" class="form-control address-field" id="calle"
                                        placeholder="Ej: 2" data-required="true">
                                </div>
                                <div class="form-group">
                                    <label for="numero_casa">Número Casa *</label>
                                    <input type="text" class="form-control address-field" id="numero_casa"
                                        placeholder="Ej: 3" data-required="true">
                                </div>
                                <div class="form-group">
                                    <label for="numero_apartamento">Número Apartamento</label>
                                    <input type="text" class="form-control address-field" id="numero_apartamento"
                                        placeholder="Ej: 4 (opcional)">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-center">
                                <div class="w-100">
                                    <p class="mb-2"><strong>Ejemplo de formato:</strong></p>
                                    <p class="text-muted">Carrera 1 Calle 2 #3 Apt 4</p>
                                    <p class="text-muted small">Los campos marcados con * son obligatorios.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-success btn-sm mr-2" id="saveAddress">
                                    <i class="fas fa-save"></i> Guardar Dirección
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="cancelAddress">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- Versión completa para inscripción --}}
    <div class="card card-success mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-id-card mr-2"></i> Datos personales </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipo_documento" class="form-label">Tipo de Documento *</label>
                    <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                        <option value="">Seleccione...</option>
                        @if($tiposDocumento && $tiposDocumento->count() > 0)
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == $tipo->id ? 'selected' : '' }}>
                                    {{ ucwords(strtolower(str_replace('_', ' ', $tipo->name))) }}
                                </option>
                            @endforeach
                        @else
                            <option value="3" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '3' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="4" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '4' ? 'selected' : '' }}>Cédula de Extranjería</option>
                            <option value="5" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '5' ? 'selected' : '' }}>Pasaporte</option>
                            <option value="6" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '6' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                            <option value="7" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '7' ? 'selected' : '' }}>Registro Civil</option>
                            <option value="8" {{ old('tipo_documento', $userData['tipo_documento'] ?? '') == '8' ? 'selected' : '' }}>Sin Identificación</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="numero_documento" class="form-label">Número de Documento *</label>
                    <input type="text" class="form-control" id="numero_documento"
                           name="numero_documento" value="{{ old('numero_documento', $userData['numero_documento'] ?? '') }}" required>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="primer_nombre" class="form-label">Primer Nombre *</label>
                    <input type="text" class="form-control" id="primer_nombre" name="primer_nombre"
                           value="{{ old('primer_nombre', $userData['primer_nombre'] ?? '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                    <input type="text" class="form-control" id="segundo_nombre"
                           name="segundo_nombre" value="{{ old('segundo_nombre', $userData['segundo_nombre'] ?? '') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="primer_apellido" class="form-label">Primer Apellido *</label>
                    <input type="text" class="form-control" id="primer_apellido"
                           name="primer_apellido" value="{{ old('primer_apellido', $userData['primer_apellido'] ?? '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                    <input type="text" class="form-control" id="segundo_apellido"
                           name="segundo_apellido" value="{{ old('segundo_apellido', $userData['segundo_apellido'] ?? '') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card card-success mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-birthday-cake mr-2"></i>Información Personal</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                <input type="date" class="form-control" id="fecha_nacimiento"
                       name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $userData['fecha_nacimiento'] ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="genero" class="form-label">Género *</label>
                <select class="form-control" id="genero" name="genero" required>
                    <option value="">Seleccione...</option>
                    @if($generos && $generos->count() > 0)
                        @foreach($generos as $genero)
                            <option value="{{ $genero->id }}" {{ old('genero', $userData['genero'] ?? '') == $genero->id ? 'selected' : '' }}>
                                {{ ucwords(strtolower(str_replace('_', ' ', $genero->name))) }}
                            </option>
                        @endforeach
                    @else
                        <option value="9" {{ old('genero', $userData['genero'] ?? '') == '9' ? 'selected' : '' }}>Masculino</option>
                        <option value="10" {{ old('genero', $userData['genero'] ?? '') == '10' ? 'selected' : '' }}>Femenino</option>
                        <option value="11" {{ old('genero', $userData['genero'] ?? '') == '11' ? 'selected' : '' }}>No Define</option>
                    @endif
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono Fijo</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $userData['telefono'] ?? '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="celular" class="form-label">Celular *</label>
                    <input type="tel" class="form-control" id="celular" name="celular"
                           value="{{ old('celular', $userData['celular'] ?? '') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico *</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $userData['email'] ?? '') }}" required>
            </div>
        </div>
    </div>

    <div class="card card-success mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Ubicación Actual</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="pais_id" class="form-label">País *</label>
                    <select class="form-control" id="pais_id" name="pais_id" required>
                        <option value="">Seleccione...</option>
                        @foreach ($paises ?? [] as $pais)
                            <option value="{{ $pais->id }}" {{ old('pais_id', $userData['pais_id'] ?? '') == $pais->id ? 'selected' : '' }}>{{ $pais->pais }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="departamento_id" class="form-label">Departamento *</label>
                    <select class="form-control" id="departamento_id" name="departamento_id" required>
                        <option value="">Seleccione...</option>
                        @foreach ($departamentos ?? [] as $departamento)
                            <option value="{{ $departamento->id }}" {{ old('departamento_id', $userData['departamento_id'] ?? '') == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="municipio_id" class="form-label">Municipio *</label>
                    <select class="form-control" id="municipio_id" name="municipio_id" required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección *</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="{{ old('direccion', $userData['direccion'] ?? '') }}" placeholder="Dirección completa" required readonly>
                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="toggleAddressForm" aria-expanded="false" aria-controls="addressForm">
                    <i class="fas fa-edit"></i> Ingresar Dirección
                </button>
            </div>
            <div id="addressForm" class="collapse mt-3" aria-labelledby="addressFormLabel">
                <div class="card card-outline-secondary">
                    <div class="card-header">
                        <h5 id="addressFormLabel" class="mb-0">Ingresar Dirección</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="carrera">Carrera *</label>
                                    <input type="text" class="form-control address-field" id="carrera"
                                        placeholder="Ej: 1" data-required="true">
                                </div>
                                <div class="form-group">
                                    <label for="calle">Calle *</label>
                                    <input type="text" class="form-control address-field" id="calle"
                                        placeholder="Ej: 2" data-required="true">
                                </div>
                                <div class="form-group">
                                    <label for="numero_casa">Número Casa *</label>
                                    <input type="text" class="form-control address-field" id="numero_casa"
                                        placeholder="Ej: 3" data-required="true">
                                </div>
                                <div class="form-group">
                                    <label for="numero_apartamento">Número Apartamento</label>
                                    <input type="text" class="form-control address-field" id="numero_apartamento"
                                        placeholder="Ej: 4 (opcional)">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-center">
                                <div class="w-100">
                                    <p class="mb-2"><strong>Ejemplo de formato:</strong></p>
                                    <p class="text-muted">Carrera 1 Calle 2 #3 Apt 4</p>
                                    <p class="text-muted small">Los campos marcados con * son obligatorios.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-success btn-sm mr-2" id="saveAddress">
                                    <i class="fas fa-save"></i> Guardar Dirección
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="cancelAddress">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
