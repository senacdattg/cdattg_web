@extends('adminlte::master')

@section('adminlte_css')
    @yield('css')
@stop

@section('classes_body', 'lockscreen')

@php
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');
    $dashboardUrl = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home');

    if (config('adminlte.use_route_url', false)) {
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
        $dashboardUrl = $dashboardUrl ? route($dashboardUrl) : '';
    } else {
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
        $dashboardUrl = $dashboardUrl ? url($dashboardUrl) : '';
    }
@endphp

@section('body')
    <div class="lockscreen-wrapper">

        {{-- Lockscreen logo --}}
        <div class="lockscreen-logo">
            <a href="{{ $dashboardUrl }}">
                <img src="{{ asset(config('adminlte.logo_img')) }}" height="50">
                {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
            </a>
        </div>

        {{-- Lockscreen user name --}}
        @auth
            <div class="lockscreen-name">
                {{ Auth::user()->name ?? Auth::user()->email }}
            </div>
        @else
            <div class="lockscreen-name">
                Usuario
            </div>
        @endauth

        {{-- Lockscreen item --}}
        <div class="lockscreen-item">
            @auth
                @if (config('adminlte.usermenu_image'))
                    <div class="lockscreen-image">
                        <img src="{{ Auth::user()->adminlte_image() }}" alt="{{ Auth::user()->name }}">
                    </div>
                @endif
            @endauth

            <form method="POST" action="{{ route('auth.password.confirm.store') }}"
                class="lockscreen-credentials @if (!config('adminlte.usermenu_image')) ml-0 @endif">
                @csrf

                <div class="input-group">
                    <div class="input-group-prepend d-none d-sm-flex">
                        <button type="button" id="passwordToggle" class="btn btn-outline-secondary"
                            aria-label="Mostrar u ocultar contraseña">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" required
                        autofocus autocomplete="current-password" aria-describedby="passwordToggle">

                    <div class="input-group-append">
                        <button type="submit" class="btn btn-success" aria-label="Confirmar contraseña">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted mt-2 text-center">
                    Por seguridad, confirme su contraseña para continuar.
                </small>
            </form>
        </div>

        {{-- Password error alert --}}
        @error('password')
            <div class="lockscreen-subitem text-center" role="alert">
                <b class="text-danger">{{ $message }}</b>
            </div>
        @enderror

        {{-- Help block --}}
        <div class="help-block text-center">
            Necesitamos confirmar su contraseña antes de continuar.
        </div>

        {{-- Additional links --}}
        <div class="text-center">
            <a href="{{ $passResetUrl }}">
                ¿Olvidó su contraseña?
            </a>
            @auth
                <form action="{{ route('logout') }}" method="POST" class="d-inline-block ml-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i> Cerrar sesión
                    </button>
                </form>
            @endauth
        </div>

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
    <script>
        (function() {
            const input = document.getElementById('password');
            const toggleBtn = document.getElementById('passwordToggle');
            if (input && toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const isPwd = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPwd ? 'text' : 'password');
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                    input.focus();
                });
            }
        })();
    </script>
@stop
