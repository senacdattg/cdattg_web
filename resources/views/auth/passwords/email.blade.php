@extends('layouts.master-layout-registro')

@section('content')
    <div class="login-box">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header text-center">
                <h1>Recuperar contraseña</h1>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Correo</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="email"
                               placeholder="correo@dominio.com">
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row d-flex justify-content-center">
                        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                            <button type="submit" class="btn btn-outline-primary btn-block">
                                Enviar enlace de restablecimiento
                            </button>
                        </div>
                    </div>
                </form>

                <div class="row mt-3 d-flex justify-content-center">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                        <a href="{{ route('login.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al inicio de sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


