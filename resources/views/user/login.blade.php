@extends('layouts.master-layout-registro')

@section('content')
    <div class="login-box">
        <div class="card card-outline shadow-lg" style="border-color: #00794d;">
            <div class="card-header text-center" style="background: linear-gradient(135deg, #00794d 0%, #005235 100%);">
                {{-- Logo del SENA --}}
                <div class="mb-3">
                    <img src="{{ asset('dist/img/LogoSena.png') }}" alt="Logo del SENA" class="img-fluid"
                        style="max-width: 180px; height: auto; filter: brightness(0) invert(1);">
                </div>
                <h2 class="mb-0 text-white font-weight-bold">Bienvenido</h2>
                <p class="text-white-50 mb-0 mt-2">Sistema de Gestión Académica</p>
            </div>

            <div class="card-body login-card-body">
                <p class="login-box-msg text-center mb-4">
                    <i class="fas fa-sign-in-alt mr-2" style="color: #00794d;"></i>
                    <strong>Inicie sesión para continuar</strong>
                </p>

                {{-- Mostrar mensajes de éxito o error --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Éxito:</strong> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm alert-no-autodismiss"
                         role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Error:</strong> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm alert-no-autodismiss"
                         role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Advertencia:</strong> {{ session('warning') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show shadow-sm alert-no-autodismiss"
                         role="alert">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Información:</strong> {{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Formulario para reenviar correo de verificación --}}
                @if (session('error') && str_contains(session('error'), 'verificar tu correo electrónico'))
                    <div class="alert alert-warning shadow-sm mb-3" role="alert">
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
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm alert-no-autodismiss"
                         role="alert">
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

                {{-- Formulario de inicio de sesión --}}
                <form action="{{ route('iniciarSesion') }}" method="POST" id="loginForm">
                    @csrf
                    {{-- Campo hidden para el parámetro de redirección --}}
                    <input type="hidden" name="redirect" value="{{ request()->query('redirect') }}">

                    {{-- Correo Institucional --}}
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-white"
                                  style="background: #00794d; border-color: #00794d;">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                        <input type="email"
                               class="form-control form-control-lg @error('email') is-invalid @enderror"
                               name="email"
                               id="email"
                               placeholder="correo@dominio.com"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               inputmode="email"
                               aria-describedby="emailHelp">
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                    <small id="emailHelp" class="form-text text-muted mb-3">
                        <i class="fas fa-info-circle mr-1"></i>Use su correo registrado en el sistema
                    </small>

                    {{-- Contraseña --}}
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-white"
                                  style="background: #00794d; border-color: #00794d;">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <input type="password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                               name="password"
                               id="password"
                               placeholder="Ingrese su contraseña"
                               required
                               autocomplete="current-password"
                               aria-describedby="passwordToggle">
                        <div class="input-group-append">
                            <button type="button"
                                    id="passwordToggle"
                                    class="btn btn-outline-secondary border-left-0"
                                    aria-label="Mostrar u ocultar contraseña"
                                    tabindex="-1">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Recordarme y Olvidó contraseña --}}
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="icheck-primary">
                                <input type="checkbox"
                                       id="remember"
                                       name="remember"
                                       value="1"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember" class="mb-0">
                                    Recordarme
                                </label>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" style="color: #00794d;">
                                    <i class="fas fa-key mr-1"></i>¿Olvidó su contraseña?
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Botón de inicio de sesión --}}
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="submit"
                                    id="loginSubmit"
                                    class="btn btn-lg btn-block btn-flat shadow-sm"
                                    style="background: linear-gradient(135deg, #00794d 0%, #005235 100%);
                                            border: none; color: #fff;">
                                <span class="spinner-border spinner-border-sm mr-2 d-none"
                                      role="status"
                                      aria-hidden="true"></span>
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                <span class="btn-text">Iniciar Sesión</span>
                            </button>
                        </div>
                    </div>

                </form>

                {{-- Botón para volver a la página principal --}}
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('home') }}"
                           class="btn btn-outline-secondary btn-block btn-flat">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('css')
    <style>
        .login-box {
            width: 100%;
            max-width: 450px;
            margin: 5% auto;
        }

        .login-card-body {
            padding: 2rem;
        }

        .card-header {
            padding: 2rem 1.5rem;
            border-bottom: none;
        }

        .input-group-text {
            min-width: 45px;
            justify-content: center;
        }

        .form-control-lg {
            font-size: 1rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #00794d;
            box-shadow: 0 0 0 0.2rem rgba(0, 121, 77, 0.25);
        }

        #loginSubmit {
            background: linear-gradient(135deg, #00794d 0%, #005235 100%) !important;
            border: none !important;
            color: #fff !important;
            transition: all 0.3s ease;
        }

        #loginSubmit:hover {
            background: linear-gradient(135deg, #008f5d 0%, #006644 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 121, 77, 0.3);
        }

        #loginSubmit:active {
            transform: translateY(0);
        }

        #loginSubmit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            border-left: 4px solid;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }

        #passwordToggle {
            border-left: none;
            transition: all 0.2s ease;
        }

        #passwordToggle:hover {
            background-color: #e9ecef;
        }

        .icheck-primary input[type="checkbox"]:checked + label::before {
            background-color: #00794d;
            border-color: #00794d;
        }

        @media (max-width: 576px) {
            .login-box {
                margin: 2% auto;
                padding: 0 1rem;
            }

            .login-card-body {
                padding: 1.5rem;
            }

            .card-header {
                padding: 1.5rem 1rem;
            }
        }
    </style>
    @endpush

    @push('js')
    <script>
        (function() {
            'use strict';

            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('passwordToggle');
            const submitBtn = document.getElementById('loginSubmit');
            const form = document.getElementById('loginForm');
            const spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;
            const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;

            // Toggle password visibility
            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                    
                    // Mantener foco en el input
                    setTimeout(() => passwordInput.focus(), 10);
                });
            }

            // Form submission handling
            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    // Validación HTML5 nativa
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                        form.classList.add('was-validated');
                        return false;
                    }

                    // Si el formulario es válido, deshabilitar botón y mostrar spinner
                    // pero NO prevenir el envío del formulario
                    if (submitBtn && !submitBtn.disabled) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('disabled');
                        
                        if (spinner) {
                            spinner.classList.remove('d-none');
                        }
                        
                        if (btnText) {
                            btnText.textContent = 'Iniciando sesión...';
                        }
                    }

                    // NO hacer preventDefault() - permitir que el formulario se envíe normalmente
                });

                // Validación en tiempo real
                const inputs = form.querySelectorAll('input[required]');
                inputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        if (this.checkValidity()) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                        } else {
                            this.classList.remove('is-valid');
                            this.classList.add('is-invalid');
                        }
                    });

                    input.addEventListener('input', function() {
                        if (this.checkValidity()) {
                            this.classList.remove('is-invalid');
                        }
                    });
                });
            }

            // Auto-dismiss solo alerts de éxito después de 8 segundos
            // Los alerts de error NO se cierran automáticamente para que el usuario pueda leerlos
            const successAlerts = document.querySelectorAll('.alert-success:not(.alert-permanent)');
            successAlerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 8000);
            });
            
            // Los alerts de error y warning se mantienen visibles hasta que el usuario los cierre manualmente

            // El formulario HTML5 maneja automáticamente el Enter para enviar
            // No necesitamos interceptar el keypress

            // Inicializar iCheck para el checkbox "Recordarme"
            // Esperar a que jQuery e iCheck estén disponibles
            function initICheck() {
                if (typeof jQuery !== 'undefined' && typeof jQuery.fn.iCheck !== 'undefined') {
                    jQuery('input[type="checkbox"]').iCheck({
                        checkboxClass: 'icheckbox_square-blue',
                        radioClass: 'iradio_square-blue',
                        increaseArea: '20%'
                    });
                } else {
                    // Reintentar después de un breve delay
                    setTimeout(initICheck, 100);
                }
            }
            
            // Iniciar después de que el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initICheck);
            } else {
                initICheck();
            }

        })();
    </script>
    @endpush
@endsection
