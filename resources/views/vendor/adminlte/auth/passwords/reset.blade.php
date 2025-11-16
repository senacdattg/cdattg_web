@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
@endphp

@section('auth_header')
    <div class="d-flex flex-column align-items-center text-center">
        <h1 class="h4 mb-0">Restablecer contraseña</h1>
        <small class="text-muted">Ingrese su nueva contraseña para continuar</small>
    </div>
@endsection

@section('auth_body')
    <div class="container px-3">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <form action="{{ $passResetUrl }}" method="post">
                    @csrf

                    {{-- Token field --}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email" class="sr-only">Correo electrónico</label>
                        <div class="input-group">
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="Correo electrónico" autofocus autocomplete="username" inputmode="email">
                            <div class="input-group-append d-none d-sm-flex">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Nueva contraseña --}}
                    <div class="form-group">
                        <label for="password" class="sr-only">Nueva contraseña</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Nueva contraseña"
                                autocomplete="new-password">
                            <div class="input-group-append">
                                <button type="button" id="passwordToggle" class="btn btn-outline-secondary"
                                    aria-label="Mostrar u ocultar contraseña">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="form-group">
                        <label for="password_confirmation" class="sr-only">Confirmar contraseña</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="Confirmar contraseña" autocomplete="new-password">
                            <div class="input-group-append">
                                <button type="button" id="passwordConfirmToggle" class="btn btn-outline-secondary"
                                    aria-label="Mostrar u ocultar confirmación">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-sync-alt mr-1"></i> Restablecer contraseña
                    </button>
                    <div class="text-center mt-3">
                        <a href="{{ route('login.index') }}" class="small">Volver al inicio de sesión</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            (function() {
                const toggle = (inputId, btnId) => {
                    const input = document.getElementById(inputId);
                    const btn = document.getElementById(btnId);
                    if (!input || !btn) return;
                    btn.addEventListener('click', function() {
                        const isPwd = input.getAttribute('type') === 'password';
                        input.setAttribute('type', isPwd ? 'text' : 'password');
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.classList.toggle('fa-eye');
                            icon.classList.toggle('fa-eye-slash');
                        }
                        input.focus();
                    });
                };
                toggle('password', 'passwordToggle');
                toggle('password_confirmation', 'passwordConfirmToggle');
            })();
        </script>
    @endpush
@stop
