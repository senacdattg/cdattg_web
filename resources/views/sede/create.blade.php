@extends('adminlte::page')
@section('content')

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Crear sede</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home.index') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sede.index') }}">Sedes</a>
                            </li>
                            <li class="breadcrumb-item active">Crear sede
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-warning btn-sm" href="{{ route('sede.index') }}">
                        <i class="fas fa-arrow-left"></i>
                        </i>
                        Volver
                    </a>
                </div>
                <div class="card-header">
                    {{-- <h3 class="card-title">{{ request()->path() }}</h3> --}}
                    {{-- formulario de registro --}}
                    <h1>Crear Sede</h1>
                    <form action="{{ route('sede.store') }}" method="post">
                        @csrf

                        {{-- Tipo de Documento y Número de Documento --}}
                        <div class="row">
                            <div class="col-md-6">
                                <label for="sede">Nombre de la sede</label>
                                <input type="text" class="form-control @error('sede') is-invalid @enderror" value="{{ old('sede') }}" name="sede" placeholder="Nombre de la sede" required autofocus>
                                @error('sede')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="direccion">Direccion</label>
                                <input type="text" class="form-control @error('direccion') is-invalid @enderror " value="{{ old('direccion') }}"
                                    name="direccion" placeholder="Direccion" required>
                                    @error('direccion')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="regional_id">Regional</label>
                                <select name="regional_id" id="" class="form-control @error('regional_id') is-invalid @enderror ">
                                    <option value="" selected disabled>Seleccione una regional</option>
                                    @foreach ($regionales as $regional )
                                        <option value="{{ $regional->id }}">{{ $regional->regional }}</option>
                                    @endforeach
                                </select>
                                @error('regional_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        {{-- departamentos y municipios --}}
                        <div class="row">
                            <div class="col-md-6">
                                <label for="departamento">Departamento</label>
                                <select name="departamento_id" class="form-control" id="departamento_id">

                                </select>

                            </div>
                            <div class="col-md-6">
                                <label for="municipio_id">Municipio</label>
                                <select name="municipio_id"
                                    class="form-control @error('municipio_id')
                                    is-invalid
                                @enderror"
                                    id="municipio_id">

                                </select>
                                @error('municipio_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>
                        </div>
                        {{-- Botón de Registro --}}
                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg">Crear sede</button>
                        </div>
                    </form>

                </div>
            </div>
        </section>
    </div>
@endsection
@section('js')
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
@endsection

