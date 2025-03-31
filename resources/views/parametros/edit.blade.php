@extends('adminlte::page')

@section('css')
<style>
    .form-control {
        border-radius: 0.25rem;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 1rem;
    }
    .form-control:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
    }
    .form-label {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: #4a5568;
    }
</style>
@endsection

@section('content')
    <section class="content-header bg-light py-3 shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0 text-gray-800">{{ $parametro->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('verificarLogin') }}" class="text-primary">Inicio</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('parametro.index') }}" class="text-primary">Parámetros</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $parametro->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('parametro.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Parámetro
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('parametro.update', $parametro->id) }}" class="row">
                                @csrf
                                @method('PUT')
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label font-weight-bold">Nombre del Parámetro</label>
                                        <input type="text" 
                                               name="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $parametro->name) }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label font-weight-bold">Estado</label>
                                        <select name="status" 
                                                class="form-control @error('status') is-invalid @enderror" 
                                                required>
                                            <option value="1" {{ $parametro->status == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ $parametro->status == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('parametro.index') }}" class="btn btn-light mr-2">
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
