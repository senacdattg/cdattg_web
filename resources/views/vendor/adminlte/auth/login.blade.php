@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    {{-- iCheck Bootstrap no está disponible, usando estilos nativos de Bootstrap --}}
@stop

@php
    $loginUrl = View::getSection('login_url') ?? route('iniciarSesion');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $registerUrl = $registerUrl ? route($registerUrl) : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $registerUrl = $registerUrl ? url($registerUrl) : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
@endphp

@section('auth_header')
    <div class="text-center">
        @if (config('adminlte.auth_logo.enabled', false))
            <img src="{{ asset(config('adminlte.auth_logo.img.path')) }}"
                 alt="{{ config('adminlte.auth_logo.img.alt') }}"
                 @if (config('adminlte.auth_logo.img.class', null))
                    class="{{ config('adminlte.auth_logo.img.class') }} mb-3"
                 @else
                    class="mb-3"
                 @endif
                 style="max-width: 200px; height: auto;">
        @else
            <img src="{{ asset(config('adminlte.logo_img')) }}"
                 alt="{{ config('adminlte.logo_img_alt') }}"
                 class="mb-3"
                 style="max-width: 200px; height: auto;">
        @endif
        <h4 class="mb-0 mt-2">{{ __('adminlte::adminlte.login_message') }}</h4>
    </div>
@stop

@section('auth_body')
    {{-- Mostrar mensajes de éxito o error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Éxito:</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Advertencia:</strong> {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Información:</strong> {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Formulario para reenviar correo de verificación --}}
    @if (session('error') && str_contains(session('error'), 'verificar tu correo electrónico'))
        <div class="alert alert-warning mb-3" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-envelope mr-2"></i>
                <strong>¿No recibiste el correo de verificación?</strong>
            </div>
            <p class="mb-2">Solicita un nuevo enlace de verificación ingresando tu correo electrónico:</p>
            <form action="{{ route('verification.resend.public') }}" method="POST" class="mt-2">
                @csrf
                <div class="input-group">
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="correo@dominio.com"
                           value="{{ old('email', '') }}"
                           required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-paper-plane mr-1"></i>Reenviar correo
                        </button>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>
    @endif

    {{-- Mostrar errores de validación en bloque --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Por favor corrija los siguientes errores:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ $loginUrl }}" method="post">
        @csrf
        {{-- Campo hidden para el parámetro de redirección --}}
        <input type="hidden" name="redirect" value="{{ request()->query('redirect') }}">

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label for="remember">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>
            </div>

            <div class="col-5">
                <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    @if($passResetUrl && Route::has('password.request'))
        <p class="my-0">
            <a href="{{ route('password.request') }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif

    {{-- Register link --}}
    @if($registerUrl && Route::has('register'))
        <p class="my-0">
            <a href="{{ $registerUrl }}">
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif

    {{-- Botón para volver a la página principal --}}
    @if(Route::has('home'))
        <p class="my-0 mt-2">
            <a href="{{ route('home') }}">
                <i class="fas fa-arrow-left mr-1"></i>Volver al Inicio
            </a>
        </p>
    @endif
@stop
