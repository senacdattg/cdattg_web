@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Centros de Formación"
        subtitle="Gestión de centros de formación"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Centros de Formación', 'url' => route('centros.index'), 'icon' => 'fa-cog'], ['label' => 'Editar Centro', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('centros.show', $centro->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Centro de Formación
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('centros.update', $centro->id) }}" class="row">
                                @csrf
                                @method('PUT')
                                
                                <!-- Regional -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="regional_id" class="form-label font-weight-bold">Regional</label>
                                        <select name="regional_id" id="regional_id" 
                                            class="form-control @error('regional_id') is-invalid @enderror" required>
                                            <option value="">Seleccione una regional</option>
                                            @foreach($regionales as $regional)
                                                <option value="{{ $regional->id }}" 
                                                    {{ old('regional_id', $centro->regional_id) == $regional->id ? 'selected' : '' }}>
                                                    {{ $regional->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('regional_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="form-label font-weight-bold">Nombre del Centro de Formación</label>
                                        <input type="text" name="nombre" id="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            value="{{ old('nombre', $centro->nombre) }}" required autofocus>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Teléfono -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono" class="form-label font-weight-bold">Teléfono</label>
                                        <input type="text" name="telefono" id="telefono"
                                            class="form-control @error('telefono') is-invalid @enderror"
                                            value="{{ old('telefono', $centro->telefono) }}"
                                            placeholder="Ingrese el teléfono del centro">
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Dirección -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion" class="form-label font-weight-bold">Dirección</label>
                                        <input type="text" name="direccion" id="direccion"
                                            class="form-control @error('direccion') is-invalid @enderror"
                                            value="{{ old('direccion', $centro->direccion) }}"
                                            placeholder="Ingrese la dirección del centro">
                                        @error('direccion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sitio Web -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="web" class="form-label font-weight-bold">Sitio Web</label>
                                        <input type="url" name="web" id="web"
                                            class="form-control @error('web') is-invalid @enderror"
                                            value="{{ old('web', $centro->web) }}"
                                            placeholder="https://ejemplo.com">
                                        @error('web')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label font-weight-bold">Estado</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="1" {{ $centro->status == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ $centro->status == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('centros.show', $centro->id) }}" class="btn btn-outline-secondary btn-sm mx-1">
                                            <i class="fas fa-times mr-1"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-outline-success btn-sm mx-1">
                                            <i class="fas fa-save mr-1"></i> Actualizar Centro
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
@endsection

