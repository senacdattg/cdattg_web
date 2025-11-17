@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Regionales"
        subtitle="Gestión de regionales"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Regionales', 'url' => route('regional.index'), 'icon' => 'fa-cog'], ['label' => 'Editar Regional', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('regional.show', $regional->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Regional
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('regional.update', $regional->id) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label for="nombre" class="form-label font-weight-bold">Nombre de la Regional</label>
                                    <input type="text" name="nombre" id="nombre"
                                        class="form-control @error('nombre') is-invalid @enderror"
                                        value="{{ old('nombre', $regional->nombre) }}" required autofocus>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="departamento_id" class="form-label font-weight-bold">Departamento</label>
                                    <select name="departamento_id" id="departamento_id" 
                                        class="form-control @error('departamento_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un departamento</option>
                                        @foreach($departamentos as $departamento)
                                            <option value="{{ $departamento->id }}" 
                                                {{ old('departamento_id', $regional->departamento_id) == $departamento->id ? 'selected' : '' }}>
                                                {{ $departamento->departamento }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departamento_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status" class="form-label font-weight-bold">Estado</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="1" {{ $regional->status == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ $regional->status == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>

                                <hr class="mt-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('regional.show', $regional->id) }}" class="btn btn-outline-secondary btn-sm mx-1">
                                        <i class="fas fa-times mr-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-outline-success btn-sm mx-1">
                                        <i class="fas fa-save mr-1"></i> Actualizar Regional
                                    </button>
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

@section('js')
    <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
    @vite(['resources/js/parametros.js'])
@endsection
