@extends('adminlte::page')

@section('css')
    @vite(['resources/css/personas.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-user-edit text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Persona</h1>
                        <p class="text-muted mb-0 font-weight-light">Edición de datos personales</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('personas.index') }}" class="link_right_header">
                                    <i class="fas fa-users"></i> Personas
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-edit"></i> Editar persona
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('personas.show', $persona->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Datos Personales
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('personas.update', $persona->id) }}" class="row">
                                @csrf
                                @method('PUT')

                                <!-- Documento -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_documento" class="form-label font-weight-bold">Tipo de
                                            Documento</label>
                                        <select name="tipo_documento" id="tipo_documento"
                                            class="form-control @error('tipo_documento') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un tipo de documento
                                            </option>
                                            @foreach ($documentos->parametros as $documento)
                                                <option value="{{ $documento->id }}"
                                                    {{ old('tipo_documento', $persona->tipo_documento) == $documento->id ? 'selected' : '' }}>
                                                    {{ $documento->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipo_documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_documento" class="form-label font-weight-bold">Número de
                                            Documento</label>
                                        <input type="text" id="numero_documento" name="numero_documento"
                                            class="form-control @error('numero_documento') is-invalid @enderror"
                                            value="{{ old('numero_documento', $persona->numero_documento) }}" />
                                        @error('numero_documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nombres -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="primer_nombre" class="form-label font-weight-bold">Primer Nombre</label>
                                        <input type="text" id="primer_nombre" name="primer_nombre"
                                            class="form-control @error('primer_nombre') is-invalid @enderror"
                                            value="{{ old('primer_nombre', $persona->primer_nombre) }}" />
                                        @error('primer_nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="segundo_nombre" class="form-label font-weight-bold">Segundo
                                            Nombre</label>
                                        <input type="text" id="segundo_nombre" name="segundo_nombre"
                                            class="form-control @error('segundo_nombre') is-invalid @enderror"
                                            value="{{ old('segundo_nombre', $persona->segundo_nombre) }}" />
                                        @error('segundo_nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Apellidos -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="primer_apellido" class="form-label font-weight-bold">Primer
                                            Apellido</label>
                                        <input type="text" id="primer_apellido" name="primer_apellido"
                                            class="form-control @error('primer_apellido') is-invalid @enderror"
                                            value="{{ old('primer_apellido', $persona->primer_apellido) }}" />
                                        @error('primer_apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="segundo_apellido" class="form-label font-weight-bold">Segundo
                                            Apellido</label>
                                        <input type="text" id="segundo_apellido" name="segundo_apellido"
                                            class="form-control @error('segundo_apellido') is-invalid @enderror"
                                            value="{{ old('segundo_apellido', $persona->segundo_apellido) }}" />
                                        @error('segundo_apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Fecha de Nacimiento y Género -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_nacimiento" class="form-label font-weight-bold">Fecha de
                                            Nacimiento</label>
                                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                                            class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                            value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento) }}" />
                                        @error('fecha_nacimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="genero" class="form-label font-weight-bold">Género</label>
                                        <select name="genero" id="genero"
                                            class="form-control @error('genero') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un género</option>
                                            @foreach ($generos->parametros as $genero)
                                                <option value="{{ $genero->id }}"
                                                    {{ old('genero', $persona->genero) == $genero->id ? 'selected' : '' }}>
                                                    {{ $genero->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('genero')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contacto -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono" class="form-label font-weight-bold">Teléfono</label>
                                        <input type="text" id="telefono" name="telefono"
                                            class="form-control @error('telefono') is-invalid @enderror"
                                            value="{{ old('telefono', $persona->telefono) }}" />
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="celular" class="form-label font-weight-bold">Celular</label>
                                        <input type="text" id="celular" name="celular"
                                            class="form-control @error('celular') is-invalid @enderror"
                                            value="{{ old('celular', $persona->celular) }}" />
                                        @error('celular')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label font-weight-bold">Correo
                                            Electrónico</label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $persona->email) }}" />
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Ubicación -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pais_id" class="form-label font-weight-bold">País</label>
                                        <select name="pais_id" id="pais_id"
                                            class="form-control @error('pais_id') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un país</option>
                                            @foreach ($paises as $pais)
                                                <option value="{{ $pais->id }}"
                                                    {{ old('pais_id', $persona->pais_id) == $pais->id ? 'selected' : '' }}>
                                                    {{ $pais->pais }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pais_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="departamento_id"
                                            class="form-label font-weight-bold">Departamento</label>
                                        <select name="departamento_id" id="departamento_id"
                                            class="form-control @error('departamento_id') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un departamento</option>
                                            @foreach ($departamentos as $departamento)
                                                <option value="{{ $departamento->id }}"
                                                    {{ old('departamento_id', $persona->departamento_id) == $departamento->id ? 'selected' : '' }}>
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
                                        <label for="municipio_id" class="form-label font-weight-bold">Municipio</label>
                                        <select name="municipio_id" id="municipio_id"
                                            class="form-control @error('municipio_id') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un municipio</option>
                                            @foreach ($municipios as $municipio)
                                                <option value="{{ $municipio->id }}"
                                                    {{ old('municipio_id', $persona->municipio_id) == $municipio->id ? 'selected' : '' }}>
                                                    {{ $municipio->municipio }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('municipio_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion" class="form-label font-weight-bold">Dirección</label>
                                        <input type="text" id="direccion" name="direccion"
                                            class="form-control @error('direccion') is-invalid @enderror"
                                            value="{{ old('direccion', $persona->direccion) }}" />
                                        @error('direccion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('personas.show', $persona->id) }}" class="btn btn-light mr-2">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Manejar cambio de país
            $('#pais_id').change(function() {
                var paisId = $(this).val();
                if (paisId) {
                    // Limpiar y deshabilitar departamentos y municipios
                    $('#departamento_id').empty().prop('disabled', true);
                    $('#municipio_id').empty().prop('disabled', true);

                    // Cargar departamentos
                    $.get('/departamentos/' + paisId, function(data) {
                        $('#departamento_id').prop('disabled', false);
                        $('#departamento_id').append(
                            '<option value="" selected disabled>Seleccione un departamento</option>'
                        );
                        $.each(data, function(key, value) {
                            var selected = value.id ==
                                '{{ old('departamento_id', $persona->departamento_id) }}' ?
                                'selected' : '';
                            $('#departamento_id').append('<option value="' + value.id +
                                '" ' + selected + '>' + value.departamento + '</option>'
                            );
                        });

                        // Si hay un departamento seleccionado, cargar sus municipios
                        if ($('#departamento_id').val()) {
                            $('#departamento_id').trigger('change');
                        }
                    });
                } else {
                    $('#departamento_id').empty().prop('disabled', true);
                    $('#municipio_id').empty().prop('disabled', true);
                }
            });

            // Manejar cambio de departamento
            $('#departamento_id').change(function() {
                var departamentoId = $(this).val();
                if (departamentoId) {
                    // Limpiar y deshabilitar municipios
                    $('#municipio_id').empty().prop('disabled', true);

                    // Cargar municipios
                    $.get('/municipios/' + departamentoId, function(data) {
                        $('#municipio_id').prop('disabled', false);
                        $('#municipio_id').append(
                            '<option value="" selected disabled>Seleccione un municipio</option>'
                        );
                        $.each(data, function(key, value) {
                            var selected = value.id ==
                                '{{ old('municipio_id', $persona->municipio_id) }}' ?
                                'selected' : '';
                            $('#municipio_id').append('<option value="' + value.id + '" ' +
                                selected + '>' + value.municipio + '</option>');
                        });
                    });
                } else {
                    $('#municipio_id').empty().prop('disabled', true);
                }
            });

            // Si hay un país seleccionado al cargar la página, cargar sus departamentos
            if ($('#pais_id').val()) {
                $('#pais_id').trigger('change');
            }
        });
    </script>
@stop
