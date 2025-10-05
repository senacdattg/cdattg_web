@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-chalkboard-teacher text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Instructor</h1>
                        <p class="text-muted mb-0 font-weight-light">Edición del instructor</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home.index') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('instructor.index') }}" class="link_right_header">
                                    <i class="fas fa-chalkboard-teacher"></i> Instructores
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-edit"></i> Editar instructor
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
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('instructor.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <form action="{{ route('instructor.update', $instructor->id) }}" method="post">
                        @csrf
                        @method('put')

                        {{-- Información Personal --}}
                        <div class="card shadow-sm no-hover mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-user mr-2"></i>Información Personal
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Tipo de Documento y Número de Documento --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tipo_documento" class="form-label font-weight-bold">Tipo de Documento</label>
                                            <select class="form-control @error('tipo_documento') is-invalid @enderror"
                                                name="tipo_documento" autofocus>
                                                <option value="" disabled selected>Seleccione un tipo de documento</option>
                                                @forelse ($documentos->parametros as $parametro)
                                                    <option value="{{ $parametro->id }}"
                                                        @if ($instructor->persona->tipo_documento == $parametro->id) selected @endif>
                                                        {{ $parametro->name }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No existe</option>
                                                @endforelse
                                            </select>
                                            @error('tipo_documento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="numero_documento" class="form-label font-weight-bold">Número de Documento</label>
                                            <input type="text"
                                                class="form-control @error('numero_documento') is-invalid @enderror"
                                                value="{{ old('numero_documento', $instructor->persona->numero_documento) }}"
                                                name="numero_documento" placeholder="Número de Documento">
                                            @error('numero_documento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Nombres y Apellidos --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primer_nombre" class="form-label font-weight-bold">Primer Nombre</label>
                                            <input type="text"
                                                class="form-control @error('primer_nombre') is-invalid @enderror"
                                                value="{{ old('primer_nombre', $instructor->persona->primer_nombre) }}"
                                                placeholder="Primer Nombre" name="primer_nombre">
                                            @error('primer_nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="segundo_nombre" class="form-label font-weight-bold">Segundo Nombre</label>
                                            <input type="text"
                                                class="form-control @error('segundo_nombre') is-invalid @enderror"
                                                value="{{ old('segundo_nombre', $instructor->persona->segundo_nombre) }}"
                                                placeholder="Segundo Nombre" name="segundo_nombre">
                                            @error('segundo_nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primer_apellido" class="form-label font-weight-bold">Primer Apellido</label>
                                            <input type="text"
                                                class="form-control @error('primer_apellido') is-invalid @enderror"
                                                value="{{ old('primer_apellido', $instructor->persona->primer_apellido) }}"
                                                placeholder="Primer Apellido" name="primer_apellido">
                                            @error('primer_apellido')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="segundo_apellido" class="form-label font-weight-bold">Segundo Apellido</label>
                                            <input type="text"
                                                class="form-control @error('segundo_apellido') is-invalid @enderror"
                                                value="{{ old('segundo_apellido', $instructor->persona->segundo_apellido) }}"
                                                placeholder="Segundo Apellido" name="segundo_apellido">
                                            @error('segundo_apellido')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Información Adicional --}}
                        <div class="card shadow-sm no-hover mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-info-circle mr-2"></i>Información Adicional
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="genero" class="form-label font-weight-bold">Género</label>
                                            <select class="form-control @error('genero') is-invalid @enderror" name="genero">
                                                <option value="" disabled selected>Seleccione un género</option>
                                                @forelse ($generos->parametros as $parametro)
                                                    <option value="{{ $parametro->id }}"
                                                        @if ($parametro->id == $instructor->persona->genero) selected @endif>
                                                        {{ $parametro->name }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No existe</option>
                                                @endforelse
                                            </select>
                                            @error('genero')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_de_nacimiento" class="form-label font-weight-bold">Fecha de Nacimiento</label>
                                            <input type="date"
                                                class="form-control @error('fecha_de_nacimiento') is-invalid @enderror"
                                                value="{{ old('fecha_de_nacimiento', $instructor->persona->fecha_de_nacimiento) }}"
                                                name="fecha_de_nacimiento" placeholder="Fecha de Nacimiento">
                                            @error('fecha_de_nacimiento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label font-weight-bold">Correo Electrónico</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                placeholder="Correo electrónico"
                                                value="{{ old('email', $instructor->persona->email) }}" name="email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="regional_id" class="form-label font-weight-bold">Regional</label>
                                            <select name="regional_id"
                                                class="form-control @error('regional_id') is-invalid @enderror">
                                                <option value="">Seleccione una regional</option>
                                                @foreach ($regionales as $regional)
                                                    <option value="{{ $regional->id }}"
                                                        @if ($regional->id == $instructor->regional_id) selected @endif>
                                                        {{ $regional->regional }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('regional_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="card shadow-sm no-hover">
                            <div class="card-body">
                                <hr class="mt-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('instructor.index') }}" class="btn btn-light mr-2">
                                        <i class="fas fa-times mr-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Actualizar Instructor
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection