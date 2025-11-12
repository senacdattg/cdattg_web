@extends('layout.master-layout-registro')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-user-plus mr-2"></i>Inscripción General</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active">Inscripción General</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-8">

                        <!-- Alertas -->
                        @if (session('success'))
                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">&times;</button>
                                <i class="icon fas fa-check"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">&times;</button>
                                <i class="icon fas fa-ban"></i> {{ session('error') }}
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-header"
                                style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                                <h3 class="card-title">Registro de datos personales y caracterización</h3>
                            </div>

                            <form action="{{ route('inscripcion.procesar') }}" method="post" id="inscripcionForm">
                                @csrf
                                <div class="card-body">

                                    <!-- Documento de Identidad -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-dark mb-3">
                                                <i class="fas fa-id-card mr-2"></i>Documento de Identidad
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="tipo_documento">Tipo de Documento *</label>
                                                <select class="form-control" name="tipo_documento" id="tipo_documento"
                                                    required>
                                                    <option value="1"
                                                        {{ old('tipo_documento') == '1' ? 'selected' : '' }}>Cédula de
                                                        Ciudadanía</option>
                                                    <option value="2"
                                                        {{ old('tipo_documento') == '2' ? 'selected' : '' }}>Tarjeta de
                                                        Identidad</option>
                                                    <option value="3"
                                                        {{ old('tipo_documento') == '3' ? 'selected' : '' }}>Cédula de
                                                        Extranjería</option>
                                                    <option value="4"
                                                        {{ old('tipo_documento') == '4' ? 'selected' : '' }}>Pasaporte
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="numero_documento">Número de Documento *</label>
                                                <input type="text" class="form-control" id="numero_documento"
                                                    value="{{ old('numero_documento') }}" name="numero_documento"
                                                    placeholder="Número de documento" required inputmode="numeric">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nombres y Apellidos -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-dark mb-3">
                                                <i class="fas fa-user mr-2"></i>Nombres y Apellidos
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="primer_nombre">Primer Nombre *</label>
                                                <input type="text" class="form-control" id="primer_nombre"
                                                    value="{{ old('primer_nombre') }}" placeholder="Primer Nombre"
                                                    name="primer_nombre" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="segundo_nombre">Segundo Nombre</label>
                                                <input type="text" class="form-control" id="segundo_nombre"
                                                    value="{{ old('segundo_nombre') }}" placeholder="Segundo Nombre"
                                                    name="segundo_nombre">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="primer_apellido">Primer Apellido *</label>
                                                <input type="text" class="form-control" id="primer_apellido"
                                                    value="{{ old('primer_apellido') }}" placeholder="Primer Apellido"
                                                    name="primer_apellido" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="segundo_apellido">Segundo Apellido</label>
                                                <input type="text" class="form-control" id="segundo_apellido"
                                                    value="{{ old('segundo_apellido') }}" placeholder="Segundo Apellido"
                                                    name="segundo_apellido">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información Personal -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-dark mb-3">
                                                <i class="fas fa-info-circle mr-2"></i>Información Personal
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                                                <input type="date" class="form-control" id="fecha_nacimiento"
                                                    value="{{ old('fecha_nacimiento') }}" name="fecha_nacimiento"
                                                    max="{{ date('Y-m-d', strtotime('-14 years')) }}" required>
                                                <small class="form-text text-muted">Debe tener al menos 14 años para
                                                    registrarse.</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="genero">Género *</label>
                                                <select class="form-control" id="genero" name="genero" required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="1" {{ old('genero') == '1' ? 'selected' : '' }}>
                                                        Masculino</option>
                                                    <option value="2" {{ old('genero') == '2' ? 'selected' : '' }}>
                                                        Femenino</option>
                                                    <option value="3" {{ old('genero') == '3' ? 'selected' : '' }}>
                                                        Otro</option>
                                                    <option value="4" {{ old('genero') == '4' ? 'selected' : '' }}>
                                                        Prefiero no decir</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contacto -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-dark mb-3">
                                                <i class="fas fa-phone mr-2"></i>Información de Contacto
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono Fijo</label>
                                                <input type="tel" class="form-control" id="telefono"
                                                    value="{{ old('telefono') }}" name="telefono"
                                                    placeholder="Teléfono fijo" inputmode="tel">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="celular">Celular *</label>
                                                <input type="tel" class="form-control" id="celular"
                                                    value="{{ old('celular') }}" name="celular"
                                                    placeholder="Número de celular" required inputmode="tel">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="email">Correo Electrónico *</label>
                                                <input type="email" class="form-control" id="email"
                                                    value="{{ old('email') }}" placeholder="Correo electrónico"
                                                    name="email" required inputmode="email">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ubicación -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-dark mb-3">
                                                <i class="fas fa-map-marker-alt mr-2"></i>Ubicación Actual
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="pais_id">País *</label>
                                                <select class="form-control" name="pais_id" id="pais_id" required>
                                                    <option value="">Seleccione...</option>
                                                    @foreach ($paises as $pais)
                                                        <option value="{{ $pais->id }}"
                                                            {{ old('pais_id') == $pais->id ? 'selected' : '' }}>
                                                            {{ $pais->pais }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="departamento_id">Departamento *</label>
                                                <select class="form-control" name="departamento_id" id="departamento_id"
                                                    required>
                                                    <option value="">Seleccione...</option>
                                                    @foreach ($departamentos as $departamento)
                                                        <option value="{{ $departamento->id }}"
                                                            {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>
                                                            {{ $departamento->departamento }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="municipio_id">Municipio *</label>
                                                <select class="form-control" name="municipio_id" id="municipio_id"
                                                    required>
                                                    <option value="">Seleccione...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="direccion">Dirección *</label>
                                                <input type="text" class="form-control" id="direccion"
                                                    value="{{ old('direccion') }}" name="direccion"
                                                    placeholder="Dirección completa" required readonly>
                                                <button type="button" class="btn btn-outline-secondary btn-sm mt-2"
                                                    id="toggleAddressForm" aria-expanded="false"
                                                    aria-controls="addressForm">
                                                    <i class="fas fa-edit"></i> Ingresar Dirección
                                                </button>
                                            </div>
                                            <div id="addressForm" class="collapse mt-3"
                                                aria-labelledby="addressFormLabel">
                                                <div class="card card-outline-secondary">
                                                    <div class="card-header">
                                                        <h5 id="addressFormLabel" class="mb-0">Ingresar Dirección</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="tipo_via">1. Tipo de vía principal
                                                                        *</label>
                                                                    <select class="form-control address-field"
                                                                        id="tipo_via" data-required="true">
                                                                        <option value="">Seleccione...</option>
                                                                        <option value="Carrera">Carrera</option>
                                                                        <option value="Calle">Calle</option>
                                                                        <option value="Transversal">Transversal</option>
                                                                        <option value="Diagonal">Diagonal</option>
                                                                        <option value="Avenida">Avenida</option>
                                                                        <option value="Autopista">Autopista</option>
                                                                        <option value="Circular">Circular</option>
                                                                        <option value="Vía">Vía</option>
                                                                        <option value="Pasaje">Pasaje</option>
                                                                        <option value="Manzana">Manzana</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="numero_via">2. Número o nombre de vía
                                                                        principal *</label>
                                                                    <input type="text"
                                                                        class="form-control address-field"
                                                                        id="numero_via"
                                                                        placeholder="Ej: 9A, 7 Bis, 45"
                                                                        data-required="true">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="letra_via">3. Letra principal de la
                                                                        vía</label>
                                                                    <select class="form-control address-field"
                                                                        id="letra_via">
                                                                        <option value="">(Sin letra)</option>
                                                                        <option>A</option>
                                                                        <option>B</option>
                                                                        <option>C</option>
                                                                        <option>D</option>
                                                                        <option>E</option>
                                                                        <option>F</option>
                                                                        <option>G</option>
                                                                        <option>H</option>
                                                                        <option>I</option>
                                                                        <option>J</option>
                                                                        <option>K</option>
                                                                        <option>L</option>
                                                                        <option>M</option>
                                                                        <option>N</option>
                                                                        <option>O</option>
                                                                        <option>P</option>
                                                                        <option>Q</option>
                                                                        <option>R</option>
                                                                        <option>S</option>
                                                                        <option>T</option>
                                                                        <option>U</option>
                                                                        <option>V</option>
                                                                        <option>W</option>
                                                                        <option>X</option>
                                                                        <option>Y</option>
                                                                        <option>Z</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group form-check">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="bis">
                                                                    <label class="form-check-label" for="bis">Sufijo
                                                                        BIS</label>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="letra_sufijo">Letra Sufijo</label>
                                                                    <select class="form-control address-field"
                                                                        id="letra_sufijo">
                                                                        <option value="">(Sin sufijo)</option>
                                                                        <option>A</option>
                                                                        <option>B</option>
                                                                        <option>C</option>
                                                                        <option>D</option>
                                                                        <option>E</option>
                                                                        <option>F</option>
                                                                        <option>G</option>
                                                                        <option>H</option>
                                                                        <option>I</option>
                                                                        <option>J</option>
                                                                        <option>K</option>
                                                                        <option>L</option>
                                                                        <option>M</option>
                                                                        <option>N</option>
                                                                        <option>O</option>
                                                                        <option>P</option>
                                                                        <option>Q</option>
                                                                        <option>R</option>
                                                                        <option>S</option>
                                                                        <option>T</option>
                                                                        <option>U</option>
                                                                        <option>V</option>
                                                                        <option>W</option>
                                                                        <option>X</option>
                                                                        <option>Y</option>
                                                                        <option>Z</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="cuadrante">Cuadrante</label>
                                                                    <select class="form-control address-field"
                                                                        id="cuadrante">
                                                                        <option value="">(Sin cuadrante)</option>
                                                                        <option value="N">Norte (N)</option>
                                                                        <option value="S">Sur (S)</option>
                                                                        <option value="E">Este (E)</option>
                                                                        <option value="O">Oeste (O)</option>
                                                                        <option value="NE">Noreste (NE)</option>
                                                                        <option value="NO">Noroeste (NO)</option>
                                                                        <option value="SE">Sureste (SE)</option>
                                                                        <option value="SO">Suroeste (SO)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="via_secundaria">
                                                                        4. Número de la via secundaria / intersección
                                                                    </label>
                                                                    <input type="text"
                                                                        class="form-control address-field"
                                                                        id="via_secundaria"
                                                                        placeholder="Ej: 12, 22B (opcional)">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="letra_via_secundaria">
                                                                        Letra asociada a la vía secundaria
                                                                    </label>
                                                                    <select class="form-control address-field"
                                                                        id="letra_via_secundaria">
                                                                        <option value="">(Sin letra)</option>
                                                                        <option>A</option>
                                                                        <option>B</option>
                                                                        <option>C</option>
                                                                        <option>D</option>
                                                                        <option>E</option>
                                                                        <option>F</option>
                                                                        <option>G</option>
                                                                        <option>H</option>
                                                                        <option>I</option>
                                                                        <option>J</option>
                                                                        <option>K</option>
                                                                        <option>L</option>
                                                                        <option>M</option>
                                                                        <option>N</option>
                                                                        <option>O</option>
                                                                        <option>P</option>
                                                                        <option>Q</option>
                                                                        <option>R</option>
                                                                        <option>S</option>
                                                                        <option>T</option>
                                                                        <option>U</option>
                                                                        <option>V</option>
                                                                        <option>W</option>
                                                                        <option>X</option>
                                                                        <option>Y</option>
                                                                        <option>Z</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="cuadrante_secundario">Cuadrante vía
                                                                        secundaria</label>
                                                                    <select class="form-control address-field"
                                                                        id="cuadrante_secundario">
                                                                        <option value="">(Sin cuadrante)</option>
                                                                        <option value="N">Norte (N)</option>
                                                                        <option value="S">Sur (S)</option>
                                                                        <option value="E">Este (E)</option>
                                                                        <option value="O">Oeste (O)</option>
                                                                        <option value="NE">Noreste (NE)</option>
                                                                        <option value="NO">Noroeste (NO)</option>
                                                                        <option value="SE">Sureste (SE)</option>
                                                                        <option value="SO">Suroeste (SO)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="numero_casa">
                                                                        5. Número de casa o edificio*
                                                                    </label>
                                                                    <input type="text"
                                                                        class="form-control address-field"
                                                                        id="numero_casa"
                                                                        placeholder="Ej: 34-15, 45-20, 12"
                                                                        data-required="true">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="complementos">6. Complementos</label>
                                                                    <input type="text"
                                                                        class="form-control address-field"
                                                                        id="complementos"
                                                                        placeholder="Ej: Apto 301, Bloque 2, Oficina 5 (opcional)">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="barrio">7. Barrio o vereda</label>
                                                                    <input type="text"
                                                                        class="form-control address-field" id="barrio"
                                                                        placeholder="Ej: Barrio el Centro (opcional)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="alert alert-info">
                                                                    <small>
                                                                        <strong>Ejemplo de formato:</strong><br>
                                                                        <span class="text-muted">Carrera 9A #34-15 Apto
                                                                            301, Barrio Centro</span><br>
                                                                        <span
                                                                        class="text-muted">Los campos marcados con *
                                                                            son obligatorios.</span>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button
                                                                    type="button" class="btn btn-primary btn-sm mr-2"
                                                                    id="saveAddress">
                                                                    <i class="fas fa-save"></i> Guardar Dirección
                                                                </button>
                                                                <button type="button" class="btn btn-secondary btn-sm"
                                                                    id="cancelAddress">
                                                                    <i class="fas fa-times"></i> Cancelar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Caracterización -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-dark mb-3">
                                                <i class="fas fa-tags mr-2"></i>Caracterización
                                            </h4>
                                            <p class="text-muted mb-3">Seleccione una categoría que corresponda a su
                                                situación (opcional):</p>
                                        </div>
                                        @foreach ($temasCaracterizacion ?? [] as $tema)
                                            <div class="col-12 mb-4">
                                                @foreach ($tema->parametros as $parametro)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="radio"
                                                            id="parametro_{{ $parametro->id }}" name="parametro_id"
                                                            value="{{ $parametro->id }}"
                                                            {{old('parametro_id') == $parametro->id ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="parametro_{{ $parametro->id }}">
                                                            {{ ucwords(str_replace('_', ' ', $parametro->name)) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Observaciones -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-muted mb-3">
                                                <i class="fas fa-comment mr-2"></i>Observaciones
                                            </h4>
                                            <div class="form-group">
                                                <label for="observaciones">Información Adicional</label>
                                                <textarea
                                                class="form-control" id="observaciones" name="observaciones" rows="3"
                                                placeholder="Información adicional que considere relevante...">
                                                {{ old('observaciones') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="card-footer">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="acepto_terminos"
                                            name="acepto_terminos" required>
                                        <label class="form-check-label" for="acepto_terminos">
                                            Acepto los <a href="#" data-toggle="modal"
                                                data-target="#modalTerminos">términos y condiciones</a> *
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save mr-2"></i>Registrar Datos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Cargar municipios según departamento seleccionado
        document.getElementById('departamento_id').addEventListener('change', function() {
            const departamentoId = this.value;
            const municipioSelect = document.getElementById('municipio_id');

            if (departamentoId) {
                fetch(`/municipios/${departamentoId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        municipioSelect.innerHTML = '<option value="">Seleccione...</option>';

                        // Manejar diferentes formatos de respuesta
                        let municipios = [];
                        if (Array.isArray(data)) {
                            // Si la respuesta es directamente un array
                            municipios = data;
                        } else if (data && data.data && Array.isArray(data.data)) {
                            // Si la respuesta es {success: true, data: [...]}
                            municipios = data.data;
                        } else if (data && data.municipios && Array.isArray(data.municipios)) {
                            // Si la respuesta es {success: true, municipios: [...]}
                            municipios = data.municipios;
                        }

                        municipios.forEach(municipio => {
                            const option = document.createElement('option');
                            option.value = municipio.id;
                            // El servicio devuelve 'nombre' o 'name', no 'municipio'
                            option.textContent = municipio.nombre || municipio.name || municipio
                                .municipio || '';
                            municipioSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error cargando municipios:', error);
                        municipioSelect.innerHTML = '<option value="">Error cargando municipios</option>';
                    });
            } else {
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
            }
        });

        // Convertir nombres y apellidos a mayúsculas
        const camposTexto = ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'];
        camposTexto.forEach(campo => {
            const elemento = document.getElementsByName(campo)[0];
            if (elemento) {
                elemento.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }
        });

        // Validar que solo contengan números
        function soloNumeros(event) {
            const key = event.key;
            if (event.ctrlKey || event.altKey || event.metaKey) {
                return true;
            }
            if (!/^\d$/.test(key)) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Aplicar validación de solo números
        const camposNumericos = ['numero_documento', 'telefono', 'celular'];
        camposNumericos.forEach(campo => {
            const elemento = document.getElementsByName(campo)[0];
            if (elemento) {
                elemento.addEventListener('keypress', soloNumeros);
            }
        });

        // Funcionalidad del formulario de dirección estructurada
        document.getElementById('toggleAddressForm').addEventListener('click', function() {
            const addressForm = document.getElementById('addressForm');
            const isVisible = addressForm.classList.contains('show');
            const button = this;
            if (isVisible) {
                $('#addressForm').collapse('hide');
                button.setAttribute('aria-expanded', 'false');
            } else {
                $('#addressForm').collapse('show');
                button.setAttribute('aria-expanded', 'true');
            }
        });

        document.getElementById('saveAddress').addEventListener('click', function() {
            const getVal = id => (document.getElementById(id) ? document.getElementById(id).value.trim() : '');
            const getCheck = id => (document.getElementById(id) ? document.getElementById(id).checked : false);
            const getSelectText = id => {
                const el = document.getElementById(id);
                if (!el || el.selectedIndex < 0) return '';
                const text = el.options[el.selectedIndex].text || '';
                const idx = text.indexOf(' (');
                return idx > -1 ? text.substring(0, idx) : text;
            };

            const tipoVia = getVal('tipo_via');
            const numeroVia = getVal('numero_via');
            const letraVia = getVal('letra_via');
            const bis = getCheck('bis');
            const letraSufijo = getVal('letra_sufijo');
            const cuadrante = getSelectText('cuadrante');

            const viaSecundaria = getVal('via_secundaria');
            const letraViaSec = getVal('letra_via_secundaria');
            const cuadranteSec = getSelectText('cuadrante_secundario');

            const numeroCasa = getVal('numero_casa');
            const complementos = getVal('complementos');
            const barrio = getVal('barrio');

            // Validar campos obligatorios
            if (!tipoVia || !numeroVia || !numeroCasa) {
                alert(
                    'Por favor complete todos los campos obligatorios: Tipo de vía, Número de vía y Número de casa.');
                return;
            }

            // Construir la dirección
            let direccion = `${tipoVia} ${numeroVia}`;
            if (letraVia) direccion += `${letraVia}`;
            if (bis) direccion += ' BIS';
            if (letraSufijo) direccion += `${letraSufijo}`;
            if (cuadrante) direccion += ` ${cuadrante}`;

            direccion += ` #${numeroCasa}`;

            if (viaSecundaria) {
                direccion += ` ${viaSecundaria}`;
                if (letraViaSec) direccion += `${letraViaSec}`;
                if (cuadranteSec) direccion += ` ${cuadranteSec}`;
            }

            if (complementos) direccion += ` ${complementos}`;
            if (barrio) direccion += `, ${barrio}`;

            // Asignar al campo principal
            document.getElementById('direccion').value = direccion;

            // Ocultar el formulario
            $('#addressForm').collapse('hide');

            // Limpiar campos
            document.querySelectorAll('.address-field').forEach(field => {
                if (field.type === 'select-one') {
                    field.selectedIndex = 0;
                } else {
                    field.value = '';
                }
            });
            const bisEl = document.getElementById('bis');
            if (bisEl) bisEl.checked = false;
        });

        document.getElementById('cancelAddress').addEventListener('click', function() {
            // Ocultar el formulario
            $('#addressForm').collapse('hide');

            // Limpiar campos
            document.querySelectorAll('.address-field').forEach(field => field.value = '');
        });

        // Validar solo números en campos de dirección
        const addressNumericFields = ['numero_via', 'numero_casa'];
        addressNumericFields.forEach(fieldId => {
            const element = document.getElementById(fieldId);
            if (element) {
                element.addEventListener('keypress', soloNumeros);
            }
        });

        // Validación de edad mínima (14 años)
        const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
        if (fechaNacimientoInput) {
            // Calcular la fecha máxima permitida (hace 14 años)
            const hoy = new Date();
            const fechaMaxima = new Date();
            fechaMaxima.setFullYear(hoy.getFullYear() - 14);

            // Establecer el atributo max si no está ya establecido
            if (!fechaNacimientoInput.getAttribute('max')) {
                const fechaMaximaStr = fechaMaxima.toISOString().split('T')[0];
                fechaNacimientoInput.setAttribute('max', fechaMaximaStr);
            }

            // Validar cuando cambia la fecha
            fechaNacimientoInput.addEventListener('change', function() {
                const fechaSeleccionada = new Date(this.value);
                const edadMinima = new Date();
                edadMinima.setFullYear(edadMinima.getFullYear() - 14);

                if (fechaSeleccionada > edadMinima) {
                    this.setCustomValidity('Debe tener al menos 14 años para registrarse.');
                    this.classList.add('is-invalid');

                    // Mostrar mensaje de error
                    let errorMessage = this.parentElement.querySelector('.invalid-feedback');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'invalid-feedback';
                        this.parentElement.appendChild(errorMessage);
                    }
                    errorMessage.textContent = 'Debe tener al menos 14 años para registrarse.';
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');

                    // Remover mensaje de error
                    const errorMessage = this.parentElement.querySelector('.invalid-feedback');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            });

            // Validar al enviar el formulario
            const form = document.getElementById('inscripcionForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const fechaSeleccionada = new Date(fechaNacimientoInput.value);
                    const edadMinima = new Date();
                    edadMinima.setFullYear(edadMinima.getFullYear() - 14);

                    if (fechaSeleccionada > edadMinima) {
                        e.preventDefault();
                        fechaNacimientoInput.focus();
                        fechaNacimientoInput.setCustomValidity('Debe tener al menos 14 años para registrarse.');
                        fechaNacimientoInput.classList.add('is-invalid');

                        // Mostrar mensaje de error
                        let errorMessage = fechaNacimientoInput.parentElement.querySelector('.invalid-feedback');
                        if (!errorMessage) {
                            errorMessage = document.createElement('div');
                            errorMessage.className = 'invalid-feedback';
                            fechaNacimientoInput.parentElement.appendChild(errorMessage);
                        }
                        errorMessage.textContent = 'Debe tener al menos 14 años para registrarse.';

                        // Mostrar alerta
                        alert('Debe tener al menos 14 años para registrarse.');
                        return false;
                    }
                });
            }
        }
    </script>

    @include('components.modal-terminos')
@endsection
