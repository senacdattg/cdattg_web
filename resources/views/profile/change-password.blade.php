@extends('adminlte::page')

@section('title', 'Cambiar Contraseña')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Cambiar Contraseña</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('#') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Mi Perfil</a></li>
                <li class="breadcrumb-item active">Cambiar Contraseña</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-2"></i>Cambiar Contraseña
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="PUT" action="{{ route('password.change') }}">
                        @csrf
                        <div class="form-group">
                            <label for="current_password">Contraseña Actual</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <input type="password" name="current_password" id="current_password" 
                                    class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Nueva Contraseña</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                </div>
                                <input type="password" name="password" id="password" 
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="form-control">
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Actualizar Contraseña
                            </button>
                            <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.card {
    border: none;
    border-radius: 0.5rem;
}
.card-header {
    border-radius: 0.5rem 0.5rem 0 0 !important;
    color: white;
}
.input-group-text {
    border-radius: 0.25rem 0 0 0.25rem;
    background-color: #f8f9fa;
}
</style>
@stop