@extends('adminlte::page')
@section('content')
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Crear Ambiente</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home.index') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('ambiente.index') }}">Ambientes</a>
                            </li>
                            <li class="breadcrumb-item active">Crear Ambientes
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <div class="card-body">
                        <a class="btn btn-warning btn-sm" href="{{ route('ambiente.index') }}">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                        </a>
                    </div>
                    <form action="{{ route('ambiente.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 div-regional">
                                <label for="regional_id">Seleccione la Regional</label>
                                <select name="regional_id" id="regional_id" class="form-control" required autofocus>
                                    <option value="" disabled selected>Selecciona una Regional</option>
                                    @forelse ($regionales as $regional)
                                        <option value="{{ $regional->id }}">{{ $regional->regional }}</option>
                                    @empty
                                        <option value="">No hay Regionales disponibles</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="col-md-6 div-sede">
                                <label for="sede_id">Seleccione la Sede</label>
                                <select name="sede_id" id="sede_id" class="form-control" required>
                                    <option value="" disabled selected>Selecciona una Sede</option>
                                </select>
                            </div>

                            <div class="col-md-6 div-bloque">
                                <label for="bloque_id">Seleccione el Bloque</label>
                                <select name="bloque_id" id="bloque_id" class="form-control" required>
                                    <option value="" disabled selected>Selecciona un Bloque</option>
                                </select>
                            </div>

                            <div class="col-md-6 div-piso">
                                <label for="piso_id">Seleccione el Piso</label>
                                <select name="piso_id" id="piso_id" class="form-control" required>
                                    <option value="" disabled selected>Selecciona un Piso</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="title">Ambiente</label>
                                <input type="text" class="form-control" value="{{ old('title') }}" name="title"
                                    placeholder="Descripion del piso" required autofocus>
                            </div>
                        </div>
                        {{-- Botón de Registro --}}
                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg">Crear Ambiente</button>
                        </div>
                        {{-- Fin Botón de Registro --}}
                    </form>
                </div>
            </div>
        </section>
@endsection
@section('js')
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
@endsection
