@extends('layout.master-layout-registro')
@section('content')
<link rel="stylesheet" href="{{ asset('css/inscripcion_general.css') }}">

<div class="container-fluid py-3 px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-md-10 col-lg-8 col-xl-6">
            <!-- Header -->
            <div class="text-center mb-4">
                <h2 class="text-primary font-weight-bold mb-2">
                    <i class="fas fa-user-plus mr-2"></i>Inscripción General
                </h2>
                <p class="text-muted mb-0">Registro de datos personales y caracterización</p>
            </div>

            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success alert-mobile">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-mobile">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <form action="{{ route('inscripcion.procesar') }}" method="post" id="inscripcionForm">
                @csrf

                <!-- Documento de Identidad -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-id-card mr-2"></i>Documento de Identidad
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Tipo de Documento *</label>
                                <div class="input-wrapper">
                                    <select class="mobile-select" name="tipo_documento" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1" {{ old('tipo_documento') == '1' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                        <option value="2" {{ old('tipo_documento') == '2' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                        <option value="3" {{ old('tipo_documento') == '3' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                        <option value="4" {{ old('tipo_documento') == '4' ? 'selected' : '' }}>Pasaporte</option>
                                    </select>
                                    <i class="fas fa-chevron-down input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Número de Documento *</label>
                                <div class="input-wrapper">
                                    <input type="text" class="mobile-input" value="{{ old('numero_documento') }}" name="numero_documento" placeholder="Número de documento" required inputmode="numeric">
                                    <i class="fas fa-hashtag input-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nombres y Apellidos -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user mr-2"></i>Nombres y Apellidos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Primer Nombre *</label>
                                <div class="input-wrapper">
                                    <input type="text" class="mobile-input" value="{{ old('primer_nombre') }}" placeholder="Primer Nombre" name="primer_nombre" required>
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Segundo Nombre</label>
                                <div class="input-wrapper">
                                    <input type="text" class="mobile-input" value="{{ old('segundo_nombre') }}" placeholder="Segundo Nombre" name="segundo_nombre">
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Primer Apellido *</label>
                                <div class="input-wrapper">
                                    <input type="text" class="mobile-input" value="{{ old('primer_apellido') }}" placeholder="Primer Apellido" name="primer_apellido" required>
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Segundo Apellido</label>
                                <div class="input-wrapper">
                                    <input type="text" class="mobile-input" value="{{ old('segundo_apellido') }}" placeholder="Segundo Apellido" name="segundo_apellido">
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Personal -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle mr-2"></i>Información Personal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Fecha de Nacimiento *</label>
                                <div class="input-wrapper">
                                    <input type="date" class="mobile-input" value="{{ old('fecha_nacimiento') }}" name="fecha_nacimiento" required>
                                    <i class="fas fa-calendar input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Género *</label>
                                <div class="input-wrapper">
                                    <select class="mobile-select" name="genero" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1" {{ old('genero') == '1' ? 'selected' : '' }}>Masculino</option>
                                        <option value="2" {{ old('genero') == '2' ? 'selected' : '' }}>Femenino</option>
                                        <option value="3" {{ old('genero') == '3' ? 'selected' : '' }}>Otro</option>
                                        <option value="4" {{ old('genero') == '4' ? 'selected' : '' }}>Prefiero no decir</option>
                                    </select>
                                    <i class="fas fa-chevron-down input-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contacto -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-phone mr-2"></i>Información de Contacto
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Teléfono Fijo</label>
                                <div class="input-wrapper">
                                    <input type="tel" class="mobile-input" value="{{ old('telefono') }}" name="telefono" placeholder="Teléfono fijo" inputmode="tel">
                                    <i class="fas fa-phone input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mobile-form-group">
                                <label class="mobile-label">Celular *</label>
                                <div class="input-wrapper">
                                    <input type="tel" class="mobile-input" value="{{ old('celular') }}" name="celular" placeholder="Número de celular" required inputmode="tel">
                                    <i class="fas fa-mobile-alt input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 mobile-form-group">
                                <label class="mobile-label">Correo Electrónico *</label>
                                <div class="input-wrapper">
                                    <input type="email" class="mobile-input" value="{{ old('email') }}" placeholder="Correo electrónico" name="email" required inputmode="email">
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt mr-2"></i>Ubicación
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-4 mobile-form-group">
                                <label class="mobile-label">País *</label>
                                <div class="input-wrapper">
                                    <select class="mobile-select" name="pais_id" id="pais_id" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($paises as $pais)
                                            <option value="{{ $pais->id }}" {{ old('pais_id') == $pais->id ? 'selected' : '' }}>{{ $pais->pais }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mobile-form-group">
                                <label class="mobile-label">Departamento *</label>
                                <div class="input-wrapper">
                                    <select class="mobile-select" name="departamento_id" id="departamento_id" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($departamentos as $departamento)
                                            <option value="{{ $departamento->id }}" {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mobile-form-group">
                                <label class="mobile-label">Municipio *</label>
                                <div class="input-wrapper">
                                    <select class="mobile-select" name="municipio_id" id="municipio_id" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                    <i class="fas fa-chevron-down input-icon"></i>
                                </div>
                            </div>
                            <div class="col-12 mobile-form-group">
                                <label class="mobile-label">Dirección *</label>
                                <div class="input-wrapper">
                                    <input type="text" class="mobile-input" value="{{ old('direccion') }}" name="direccion" placeholder="Dirección completa" required>
                                    <i class="fas fa-home input-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Caracterización -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tags mr-2"></i>Caracterización
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Seleccione las categorías que correspondan a su situación (opcional):</p>

                        @foreach($categoriasConHijos as $categoria)
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">{{ $categoria['nombre'] }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($categoria['hijos'] as $hijo)
                                            <div class="col-12 mb-3">
                                                <div class="checkbox-group">
                                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="{{ $hijo->id }}" id="categoria_{{ $hijo->id }}"
                                                           {{ in_array($hijo->id, old('categorias', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="categoria_{{ $hijo->id }}">
                                                        {{ $hijo->nombre }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-comment mr-2"></i>Observaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mobile-form-group">
                            <label class="mobile-label">Información Adicional</label>
                            <div class="input-wrapper">
                                <textarea class="mobile-input" name="observaciones" rows="3" placeholder="Información adicional que considere relevante..." style="resize: vertical; min-height: 80px;">{{ old('observaciones') }}</textarea>
                                <i class="fas fa-comment input-icon" style="top: 20px;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body text-center">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save mr-2"></i>Registrar Datos
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Cargar municipios según departamento seleccionado
        document.getElementById('departamento_id').addEventListener('change', function() {
            const departamentoId = this.value;
            const municipioSelect = document.getElementById('municipio_id');

            if (departamentoId) {
                fetch(`/municipios/${departamentoId}`)
                    .then(response => response.json())
                    .then(data => {
                        municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
                        data.forEach(municipio => {
                            const option = document.createElement('option');
                            option.value = municipio.id;
                            option.textContent = municipio.municipio;
                            municipioSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
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
    </script>
@endsection