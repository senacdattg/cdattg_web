@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
    $passEmailUrl = View::getSection('password_email_url') ?? config('adminlte.password_email_url', 'password/email');

    if (config('adminlte.use_route_url', false)) {
        $passEmailUrl = $passEmailUrl ? route($passEmailUrl) : '';
    } else {
        $passEmailUrl = $passEmailUrl ? url($passEmailUrl) : '';
    }
@endphp

@section('auth_header')
    <div class="d-flex flex-column align-items-center text-center">
        <img src="{{ asset('dist/img/LogoSena.png') }}" alt="Logo SENA" class="img-fluid mb-2" style="max-height: 64px;">
        <h1 class="h4 mb-0">Recuperar contraseña</h1>
        <small class="text-muted">Ingresa tu correo para enviarte el enlace</small>
    </div>
@endsection

@section('auth_body')

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <form action="{{ $passEmailUrl }}" method="post">
                    @csrf

                    {{-- Correo electrónico --}}
                    <div class="form-group">
                        <label for="email" class="sr-only">Correo electrónico</label>
                        <div class="input-group">
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="Correo electrónico" autofocus autocomplete="username" inputmode="email"
                                aria-describedby="emailHelp">

                            <div class="input-group-append d-none d-sm-flex">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                </div>
                            </div>
                        </div>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <small id="emailHelp" class="form-text text-muted mb-3">
                        Te enviaremos un enlace para restablecer tu contraseña. Revisa también tu carpeta de spam.
                    </small>

                    {{-- Botón enviar enlace --}}
                    <button type="submit" class="btn btn-success btn-lg btn-block">
                        <span class="fas fa-paper-plane"></span>
                        Enviar enlace
                    </button>

                    <div class="text-center mt-3">
                        <a href="{{ route('login.index') }}" class="small">Volver al inicio de sesión</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop
