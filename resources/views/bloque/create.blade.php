@extends('adminlte::page')
@section('content')

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Crear Bloque


                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home.index') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item active">Crear Bloque
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bloque.store') }}" method="post">
                        @csrf

                        {{-- Tipo de Documento y Número de Documento --}}
                        <div class="row">

                            <div class="col-md-6 div-sede">
                                <label for="sede_id">Seleccione la sede</label>
                                <select name="sede_id" id="sede_id"
                                    class="form-control @error('sede_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Selecciona una sede</option>
                                    @forelse ($sedes as $sede)
                                        <option value="{{ $sede->id }}"{{ old('sede_id') == $sede->id ? 'selected' : '' }}>{{ $sede->sede }}</option>
                                    @empty
                                        <option value="">No hay sedes disponibles</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="bloque">Bloque</label>
                                <input type="text" class="form-control @error('bloque') is-invalid @enderror"
                                    value="{{ old('bloque') }}" name="bloque" placeholder="Nombre del bloque" required
                                    autofocus>
                            </div>
                        </div>


                        {{-- Botón de Registro --}}
                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg">Crear Bloque</button>
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
