@extends('adminlte::page')
@section('content')

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1> Crear Piso</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home.index') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('piso.index') }}">Pisos</a>
                            </li>
                            <li class="breadcrumb-item active">Crear Piso
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('piso.store') }}" method="post">
                        @csrf

                        {{-- Tipo de Documento y Número de Documento --}}
                        <div class="row">

                            <div class="col-md-6 div-regional">
                                <label for="regional_id">Seleccione la regional</label>
                                <select name="regional_id" id="regional_id" class="form-control" required autofocus>
                                    <option value="" disabled selected>Selecciona una regional</option>
                                    @forelse ($regionales as $regional)
                                        <option value="{{ $regional->id }}">{{ $regional->regional }}</option>
                                    @empty
                                        <option value="">No hay regionales disponibles</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="col-md-6 div-sede">
                                <label for="sede_id">Seleccione la sede</label>
                                <select name="sede_id" id="sede_id" class="form-control" required autofocus>
                                    <option value="" disabled selected>Selecciona una sede</option>
                                    {{-- @forelse ($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->sede }}</option>
                                    @empty
                                        <option value="">No hay sedes disponibles</option>
                                    @endforelse --}}
                                </select>
                            </div>

                            <div class="col-md-6 div-bloque">
                                <label for="bloque_id">Seleccione el bloque</label>
                                <select name="bloque_id" id="bloque_id" class="form-control" required>
                                    <option value="" disabled selected>Selecciona un bloque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="piso">Piso</label>
                                <input type="text" class="form-control" value="{{ old('piso') }}"
                                    name="piso" placeholder="Descripion del piso" required >
                            </div>
                        </div>




                        {{-- Botón de Registro --}}
                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg">Crear Piso</button>
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
