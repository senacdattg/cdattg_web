@extends('adminlte::page')

@section('title', 'Crear Persona')

@section('content_header')
    <h1>Crear Persona</h1>
@stop

@section('content')
    <div class="container">
        <section class="vh-100">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-lg-12 col-xl-11">
                        <div class="card text-black" style="border-radius: 25px;">
                            <div class="card-body p-md-5">
                                <div class="row justify-content-center">
                                    <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

                                        <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Registro de Persona</p>

                                        <form class="mx-1 mx-md-4" method="POST" action="{{ route('personas.store') }}">
                                            @csrf

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="tipo_documento">Tipo de Documento</label>
                                                    <select name="tipo_documento" id="tipo_documento"
                                                        class="form-control @error('tipo_documento') is-invalid @enderror">
                                                        <option value="" selected disabled>Seleccione un tipo de
                                                            documento
                                                        </option>
                                                        @foreach ($documentos->parametros as $documento)
                                                            <option value="{{ $documento->id }}">{{ $documento->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('tipo_documento')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="numero_documento">Número de
                                                        Documento</label>
                                                    <input type="text" id="numero_documento" name="numero_documento"
                                                        class="form-control @error('numero_documento') is-invalid @enderror"
                                                        value="{{ old('numero_documento') }}" />
                                                    @error('numero_documento')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="primer_nombre">Primer Nombre</label>
                                                    <input type="text" id="primer_nombre" name="primer_nombre"
                                                        class="form-control @error('primer_nombre') is-invalid @enderror"
                                                        value="{{ old('primer_nombre') }}" />
                                                    @error('primer_nombre')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="segundo_nombre">Segundo Nombre</label>
                                                    <input type="text" id="segundo_nombre" name="segundo_nombre"
                                                        class="form-control @error('segundo_nombre') is-invalid @enderror"
                                                        value="{{ old('segundo_nombre') }}" />
                                                    @error('segundo_nombre')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="primer_apellido">Primer Apellido</label>
                                                    <input type="text" id="primer_apellido" name="primer_apellido"
                                                        class="form-control @error('primer_apellido') is-invalid @enderror"
                                                        value="{{ old('primer_apellido') }}" />
                                                    @error('primer_apellido')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="segundo_apellido">Segundo
                                                        Apellido</label>
                                                    <input type="text" id="segundo_apellido" name="segundo_apellido"
                                                        class="form-control @error('segundo_apellido') is-invalid @enderror"
                                                        value="{{ old('segundo_apellido') }}" />
                                                    @error('segundo_apellido')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="fecha_nacimiento">Fecha de
                                                        Nacimiento</label>
                                                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                                                        class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                                        value="{{ old('fecha_nacimiento') }}" />
                                                    @error('fecha_nacimiento')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="genero">Género</label>
                                                    <select name="genero" id="genero"
                                                        class="form-control @error('genero') is-invalid @enderror">
                                                        <option value="" selected disabled>Seleccione un género
                                                        </option>
                                                        @foreach ($generos->parametros as $genero)
                                                            <option value="{{ $genero->id }}">{{ $genero->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('genero')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="telefono">Teléfono</label>
                                                    <input type="text" id="telefono" name="telefono"
                                                        class="form-control @error('telefono') is-invalid @enderror"
                                                        value="{{ old('telefono') }}" />
                                                    @error('telefono')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="celular">Celular</label>
                                                    <input type="text" id="celular" name="celular"
                                                        class="form-control @error('celular') is-invalid @enderror"
                                                        value="{{ old('celular') }}" />
                                                    @error('celular')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="email">Correo Electrónico</label>
                                                    <input type="email" id="email" name="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        value="{{ old('email') }}" />
                                                    @error('email')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="pais_id">País</label>
                                                    <select name="pais_id" id="pais_id"
                                                        class="form-control @error('pais_id') is-invalid @enderror">
                                                        <option value="" selected disabled>Seleccione un país
                                                        </option>
                                                        @foreach ($paises as $pais)
                                                            <option value="{{ $pais->id }}">{{ $pais->pais }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('pais_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="departamento_id">Departamento</label>
                                                    <select name="departamento_id" id="departamento_id"
                                                        class="form-control @error('departamento_id') is-invalid @enderror">
                                                        <option value="" selected disabled>Seleccione un departamento
                                                        </option>
                                                        @foreach ($departamentos as $departamento)
                                                            <option value="{{ $departamento->id }}">
                                                                {{ $departamento->departamento }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('departamento_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="municipio_id">Municipio</label>
                                                    <select name="municipio_id" id="municipio_id"
                                                        class="form-control @error('municipio_id') is-invalid @enderror">
                                                        <option value="" selected disabled>Seleccione un municipio
                                                        </option>
                                                        @foreach ($municipios as $municipio)
                                                            <option value="{{ $municipio->id }}">
                                                                {{ $municipio->municipio }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('municipio_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-4">
                                                <div class="form-outline flex-fill mb-0">
                                                    <label class="form-label" for="direccion">Dirección</label>
                                                    <input type="text" id="direccion" name="direccion"
                                                        class="form-control @error('direccion') is-invalid @enderror"
                                                        value="{{ old('direccion') }}" />
                                                    @error('direccion')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                                                <button type="submit" class="btn btn-primary btn-lg">Registrar</button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
@endsection
