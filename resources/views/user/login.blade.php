@extends('layouts.master-layout-registro')

@section('content')
    <div class="login-box">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header text-center">
                {{-- Logo del SENA --}}
                <img src="{{ asset('dist/img/LogoSena.png') }}" alt="Logo del sena" class="img-fluid"
                    style="max-width: 150px; height: auto;">
                <h1>Bienvenido</h1>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <p class="login-box-msg"><strong>¡Para comenzar, inicie sesión!</strong></p>
                </div>

                {{-- Mostrar mensajes de éxito o error --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Mostrar errores de validación en bloque --}}
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

                {{-- Formulario de inicio de sesión --}}
                <form action="{{ route('iniciarSesion') }}" method="POST">
                    @csrf
                    {{-- Campo hidden para el parámetro de redirección --}}
                    <input type="hidden" name="redirect" value="{{ request()->query('redirect') }}">

                    {{-- Correo Institucional --}}
                    <div class="form-group">
                        <label for="email">Usuario</label>
                        <div class="input-group">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                id="email" placeholder="correo@dominio.com" value="{{ old('email') }}" required
                                autofocus autocomplete="username" inputmode="email" aria-describedby="emailHelp">
                            <div class="input-group-append d-none d-sm-flex">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                </span>
                            </div>
                        </div>
                        <small
                            id="emailHelp"
                            class="form-text text-muted">Use su correo registrado en el sistema.</small>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" id="password" placeholder="Contraseña" required
                                autocomplete="current-password" aria-describedby="passwordToggle">
                            <div class="input-group-append">
                                <button type="button" id="passwordToggle" class="btn btn-outline-secondary"
                                    aria-label="Mostrar u ocultar contraseña">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Recordarme --}}
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>

                    {{-- Olvidó su contraseña --}}
                    @if (Route::has('password.request'))
                        <div class="d-flex justify-content-end mb-3">
                            <a href="{{ route('password.request') }}" class="small">¿Olvidó su contraseña?</a>
                        </div>
                    @endif

                    {{-- Botón de inicio de sesión --}}
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                            <button type="submit" id="loginSubmit" class="btn btn-outline-success btn-lg btn-block">
                                <span class="spinner-border spinner-border-sm mr-2 d-none" role="status"
                                    aria-hidden="true"></span>
                                Iniciar Sesión
                            </button>
                        </div>
                    </div>

                </form>

                {{-- Botón para volver a la página principal --}}
                <div class="row mt-3 d-flex justify-content-center">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('passwordToggle');
            const submitBtn = document.getElementById('loginSubmit');
            const spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                    passwordInput.focus();
                });
            }

            const form = document.querySelector('form[action="{{ route('iniciarSesion') }}"]');
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.setAttribute('disabled', 'disabled');
                    if (spinner) {
                        spinner.classList.remove('d-none');
                    }
                });
            }
        })();
    </script>
@endsection
