@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-network-wired" 
        title="Red de Conocimiento"
        subtitle="Edición de la red de conocimiento"
        :breadcrumb="[['label' => 'Redes de Conocimiento', 'url' => route('red-conocimiento.index') , 'icon' => 'fa-network-wired'], ['label' => 'Editar red de conocimiento', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('red-conocimiento.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Red de Conocimiento
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('red-conocimiento.update', $redConocimiento->id) }}" class="row">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="form-label font-weight-bold">Nombre de la Red de Conocimiento</label>
                                        <input type="text" name="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            value="{{ old('nombre', $redConocimiento->nombre) }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="regionals_id" class="form-label font-weight-bold">Regional</label>
                                        <select name="regionals_id" class="form-control @error('regionals_id') is-invalid @enderror">
                                            <option value="">Selecciona una regional (opcional)</option>
                                            @foreach ($regionales as $regional)
                                                <option value="{{ $regional->id }}" 
                                                        {{ old('regionals_id', $redConocimiento->regionals_id) == $regional->id ? 'selected' : '' }}>
                                                    {{ $regional->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('regionals_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label font-weight-bold">Estado</label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="1" {{ old('status', $redConocimiento->status) == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ old('status', $redConocimiento->status) == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('red-conocimiento.index') }}" class="btn btn-light mr-2">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Guardar Cambios
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
    @include('layout.alertas')
@endsection
