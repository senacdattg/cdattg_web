@extends('adminlte::page')

@section('title', "Editar Red de Conocimiento")

@section('content')
        <!-- Encabezado de la Página -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Editar Red de Conocimiento</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('red-conocimiento.index') }}">Redes de Conocimiento</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $redConocimiento->nombre }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenido Principal -->
        <section class="content">
            <div class="container-fluid">
                <!-- Botón Volver -->
                <div class="mb-3">
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('red-conocimiento.index') }}" title="Volver">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <!-- Card de Edición -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Editar Red de Conocimiento: {{ $redConocimiento->nombre }}</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('red-conocimiento.update', $redConocimiento->id) }}" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre de la Red de Conocimiento</label>
                                <input type="text" 
                                        id="nombre" 
                                        name="nombre" 
                                        class="form-control @error('nombre') is-invalid @enderror" 
                                        value="{{ old('nombre', $redConocimiento->nombre) }}" 
                                        required 
                                        autofocus
                                        placeholder="Ingrese el nombre de la red de conocimiento">
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="regionals_id" class="form-label">Regional</label>
                                <select id="regionals_id" 
                                        name="regionals_id" 
                                        class="form-control @error('regionals_id') is-invalid @enderror">
                                    <option value="">Selecciona una regional (opcional)</option>
                                    @foreach ($regionales as $regional)
                                        <option value="{{ $regional->id }}" 
                                                {{ old('regionals_id', $redConocimiento->regionals_id) == $regional->id ? 'selected' : '' }}>
                                            {{ $regional->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('regionals_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label">Estado</label>
                                <select id="status" 
                                        name="status" 
                                        class="form-control @error('status') is-invalid @enderror" 
                                        required>
                                    <option value="1" {{ old('status', $redConocimiento->status) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('status', $redConocimiento->status) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('red-conocimiento.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-2"></i>
                                    Actualizar Red de Conocimiento
                                </button>
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
