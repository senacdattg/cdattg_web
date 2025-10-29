@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Personas"
        subtitle="Gestión de personas del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-cog'], ['label' => 'Crear Persona', 'icon' => 'fa-plus', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Botón Volver -->
            <div class="mb-3">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('personas.index') }}" title="Volver">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-plus mr-2"></i>Registro de Persona
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('personas.store') }}" class="row">
                                @csrf

                                <!-- Documento -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="tipo_documento">Tipo de Documento</label>
                                        <select name="tipo_documento" id="tipo_documento"
                                            class="form-control @error('tipo_documento') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un tipo de documento</option>
                                            @foreach ($documentos->parametros as $documento)
                                                <option value="{{ $documento->id }}">{{ $documento->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('tipo_documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="numero_documento">Número de Documento</label>
                                        <input type="text" id="numero_documento" name="numero_documento"
                                            class="form-control @error('numero_documento') is-invalid @enderror"
                                            value="{{ old('numero_documento') }}" />
                                        @error('numero_documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nombres -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="primer_nombre">Primer Nombre</label>
                                        <input type="text" id="primer_nombre" name="primer_nombre"
                                            class="form-control @error('primer_nombre') is-invalid @enderror"
                                            value="{{ old('primer_nombre') }}" />
                                        @error('primer_nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="segundo_nombre">Segundo Nombre</label>
                                        <input type="text" id="segundo_nombre" name="segundo_nombre"
                                            class="form-control @error('segundo_nombre') is-invalid @enderror"
                                            value="{{ old('segundo_nombre') }}" />
                                        @error('segundo_nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Apellidos -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="primer_apellido">Primer Apellido</label>
                                        <input type="text" id="primer_apellido" name="primer_apellido"
                                            class="form-control @error('primer_apellido') is-invalid @enderror"
                                            value="{{ old('primer_apellido') }}" />
                                        @error('primer_apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="segundo_apellido">Segundo Apellido</label>
                                        <input type="text" id="segundo_apellido" name="segundo_apellido"
                                            class="form-control @error('segundo_apellido') is-invalid @enderror"
                                            value="{{ old('segundo_apellido') }}" />
                                        @error('segundo_apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Fecha de Nacimiento y Género -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="fecha_nacimiento">Fecha de Nacimiento</label>
                                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                                            class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                            value="{{ old('fecha_nacimiento') }}" />
                                        @error('fecha_nacimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="genero">Género</label>
                                        <select name="genero" id="genero"
                                            class="form-control @error('genero') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un género</option>
                                            @foreach ($generos->parametros as $genero)
                                                <option value="{{ $genero->id }}">{{ $genero->name }}</option>
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
                                        <label class="form-label font-weight-bold" for="telefono">Teléfono</label>
                                        <input type="text" id="telefono" name="telefono"
                                            class="form-control @error('telefono') is-invalid @enderror"
                                            value="{{ old('telefono') }}" />
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="celular">Celular</label>
                                        <input type="text" id="celular" name="celular"
                                            class="form-control @error('celular') is-invalid @enderror"
                                            value="{{ old('celular') }}" />
                                        @error('celular')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="email">Correo Electrónico</label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" />
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Ubicación -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="pais_id">País</label>
                                        <select name="pais_id" id="pais_id"
                                            class="form-control @error('pais_id') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un país</option>
                                            @foreach ($paises as $pais)
                                                <option value="{{ $pais->id }}">{{ $pais->pais }}</option>
                                            @endforeach
                                        </select>
                                        @error('pais_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="departamento_id">Departamento</label>
                                        <select name="departamento_id" id="departamento_id"
                                            class="form-control @error('departamento_id') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un departamento</option>
                                            @foreach ($departamentos as $departamento)
                                                <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                                            @endforeach
                                        </select>
                                        @error('departamento_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="municipio_id">Municipio</label>
                                        <select name="municipio_id" id="municipio_id"
                                            class="form-control @error('municipio_id') is-invalid @enderror">
                                            <option value="" selected disabled>Seleccione un municipio</option>
                                            @foreach ($municipios as $municipio)
                                                <option value="{{ $municipio->id }}">{{ $municipio->municipio }}</option>
                                            @endforeach
                                        </select>
                                        @error('municipio_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold" for="direccion">Dirección</label>
                                        <input type="text" id="direccion" name="direccion"
                                            class="form-control @error('direccion') is-invalid @enderror"
                                            value="{{ old('direccion') }}" />
                                        @error('direccion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('personas.index') }}" class="btn btn-outline-secondary btn-sm mx-1">
                                            <i class="fas fa-times mr-1"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-outline-success btn-sm mx-1">
                                            <i class="fas fa-save mr-1"></i> Registrar
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
@endsection
