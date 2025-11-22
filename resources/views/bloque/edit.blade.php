@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Bloques"
        subtitle="Gestión de bloques del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Bloques', 'url' => route('bloque.index'), 'icon' => 'fa-cog'], ['label' => 'Editar Bloque', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('bloque.show', $bloque->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Bloque
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('bloque.update', $bloque->id) }}" class="row">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sede_id" class="form-label font-weight-bold">Sede</label>
                                        <select name="sede_id" id="sede_id"
                                            class="form-control @error('sede_id') is-invalid @enderror" required>
                                            <option value="">Seleccione una sede</option>
                                            @forelse ($sedes as $sede)
                                                <option value="{{ $sede->id }}"
                                                    {{ old('sede_id', $bloque->sede_id) == $sede->id ? 'selected' : '' }}>
                                                    {{ $sede->sede }}
                                                </option>
                                            @empty
                                                <option value="">No hay sedes disponibles</option>
                                            @endforelse
                                        </select>
                                        @error('sede_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bloque" class="form-label font-weight-bold">Nombre del Bloque</label>
                                        <input type="text" 
                                               id="bloque" 
                                               name="bloque" 
                                               class="form-control @error('bloque') is-invalid @enderror"
                                               value="{{ old('bloque', $bloque->bloque) }}" 
                                               required 
                                               autofocus
                                               placeholder="Ingrese el nombre del bloque">
                                        @error('bloque')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label font-weight-bold">Estado</label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="1" {{ $bloque->status == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ $bloque->status == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('bloque.show', $bloque->id) }}" class="btn btn-outline-secondary btn-sm mx-1">
                                            <i class="fas fa-times mr-1"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-outline-success btn-sm mx-1">
                                            <i class="fas fa-save mr-1"></i> Actualizar Bloque
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
    @include('layouts.footer')
@endsection

@section('plugins.Chartjs', true)

@section('js')
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
@endsection
