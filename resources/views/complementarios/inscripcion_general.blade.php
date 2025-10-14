@extends('layout.master-layout-registro')
@section('content')
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h1><b>Inscripción General</b></h1>
                <p class="text-muted">Registro de datos personales y caracterización</p>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('inscripcion.procesar') }}" method="post">
                    @csrf

                    {{-- Tipo y número de documento --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="tipo_documento">Tipo de Documento *</label>
                            <div class="input-group mb-3">
                                <select class="form-control" name="tipo_documento" required>
                                    <option value="">Seleccione...</option>
                                    <option value="1" {{ old('tipo_documento') == '1' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="2" {{ old('tipo_documento') == '2' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                    <option value="3" {{ old('tipo_documento') == '3' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                    <option value="4" {{ old('tipo_documento') == '4' ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-id-card"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="numero_documento">Número de Documento *</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ old('numero_documento') }}" name="numero_documento" placeholder="Número de documento" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-hashtag"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nombres --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="primer_nombre">Primer Nombre *</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ old('primer_nombre') }}" placeholder="Primer Nombre" name="primer_nombre" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="segundo_nombre">Segundo Nombre</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ old('segundo_nombre') }}" placeholder="Segundo Nombre" name="segundo_nombre">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Apellidos --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="primer_apellido">Primer Apellido *</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ old('primer_apellido') }}" placeholder="Primer Apellido" name="primer_apellido" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="segundo_apellido">Segundo Apellido</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ old('segundo_apellido') }}" placeholder="Segundo Apellido" name="segundo_apellido">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fecha nacimiento y género --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" value="{{ old('fecha_nacimiento') }}" name="fecha_nacimiento" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-calendar"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="genero">Género *</label>
                            <div class="input-group mb-3">
                                <select class="form-control" name="genero" required>
                                    <option value="">Seleccione...</option>
                                    <option value="1" {{ old('genero') == '1' ? 'selected' : '' }}>Masculino</option>
                                    <option value="2" {{ old('genero') == '2' ? 'selected' : '' }}>Femenino</option>
                                    <option value="3" {{ old('genero') == '3' ? 'selected' : '' }}>Otro</option>
                                    <option value="4" {{ old('genero') == '4' ? 'selected' : '' }}>Prefiero no decir</option>
                                </select>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-venus-mars"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Teléfonos --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label for="telefono">Teléfono Fijo</label>
                            <div class="input-group mb-3">
                                <input type="tel" class="form-control" value="{{ old('telefono') }}" name="telefono" placeholder="Teléfono fijo">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-phone"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="celular">Celular *</label>
                            <div class="input-group mb-3">
                                <input type="tel" class="form-control" value="{{ old('celular') }}" name="celular" placeholder="Número de celular" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-mobile-alt"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <label for="email">Correo Electrónico *</label>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" value="{{ old('email') }}" placeholder="Correo electrónico" name="email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Ubicación --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label for="pais_id">País *</label>
                            <div class="input-group mb-3">
                                <select class="form-control" name="pais_id" id="pais_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($paises as $pais)
                                        <option value="{{ $pais->id }}" {{ old('pais_id') == $pais->id ? 'selected' : '' }}>{{ $pais->pais }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-globe"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="departamento_id">Departamento *</label>
                            <div class="input-group mb-3">
                                <select class="form-control" name="departamento_id" id="departamento_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->id }}" {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>{{ $departamento->departamento }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-map-marker"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="municipio_id">Municipio *</label>
                            <div class="input-group mb-3">
                                <select class="form-control" name="municipio_id" id="municipio_id" required>
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-city"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dirección --}}
                    <label for="direccion">Dirección *</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" value="{{ old('direccion') }}" name="direccion" placeholder="Dirección completa" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-home"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Caracterización --}}
                    <hr>
                    <h5><i class="fas fa-tags"></i> Caracterización</h5>
                    <p class="text-muted">Seleccione las categorías que correspondan a su situación (opcional):</p>

                    @foreach($categoriasConHijos as $categoria)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">{{ $categoria['nombre'] }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($categoria['hijos'] as $hijo)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
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

                    {{-- Observaciones --}}
                    <label for="observaciones">Observaciones</label>
                    <div class="input-group mb-3">
                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Información adicional que considere relevante...">{{ old('observaciones') }}</textarea>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-comment"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">Registrar Datos</button>
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