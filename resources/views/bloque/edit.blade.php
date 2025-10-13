@extends('adminlte::page')
@section('content')

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $bloque->bloque }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home.index') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('bloque.index') }}">Bloques</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $bloque->bloque }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bloque.update', ['bloque' => $bloque->id]) }}" method="post">
                        @csrf
                        @method('put')
                        {{-- Tipo de Documento y Número de Documento --}}
                        <div class="row">

                            <div class="col-md-6 div-sede">
                                <label for="sede_id">Seleccione la sede</label>
                                <select name="sede_id" id="sede_id"
                                    class="form-control @error('sede_id') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('sede_id') ? '' : ($bloque->sede_id ? '' : 'selected') }}>
                                        Selecciona una sede
                                    </option>
                                    @forelse ($sedes as $sede)
                                        <option value="{{ $sede->id }}"
                                            {{ old('sede_id') == $sede->id || $sede->id == $bloque->sede_id ? 'selected' : '' }}>
                                            {{ $sede->sede }}</option>
                                    @empty
                                        <option value="">No hay sedes disponibles</option>
                                    @endforelse
                                </select>

                            </div>

                            <div class="col-md-6">
                                <label for="bloque">Bloque</label>
                                <input type="text" class="form-control @error('bloque') is-invalid @enderror"
                                    value="{{ old('bloque', $bloque->bloque) }} " name="bloque" placeholder="Nombre del bloque" required
                                    autofocus>
                            </div>
                            <div class="col-md-6">
                                <label for="status">Estado</label>
                                <select name="status" id="" class="form-control @error('status') is-invalid @enderror">
                                    <option value="1" {{ $bloque->status == 1 ? 'selected' : '' }}>ACTIVO</option>
                                    <option value="0" {{ $bloque->status == 0 ? 'selected' : '' }}>INACTIVO</option>
                                </select>
                            </div>
                        </div>


                        {{-- Botón de Registro --}}
                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg">Actualizar Bloque</button>
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
