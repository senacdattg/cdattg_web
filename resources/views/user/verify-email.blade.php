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
                <h2 class="mb-0 text-white font-weight-bold">Verifica tu Correo</h2>
                <p class="text-white-50 mb-0 mt-2">Sistema de Gestión Académica</p>
            </div>

            <div class="card-body login-card-body">
                {{-- Mensajes de éxito o información --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Éxito:</strong> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Atención:</strong> {{ session('warning') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Información:</strong> {{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('resent'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-envelope mr-2"></i>
                        <strong>Email enviado:</strong> Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="text-center mb-4">
                    <i class="fas fa-envelope-open-text fa-3x mb-3" style="color: #00794d;"></i>
                    <h4 class="mb-3">Verifica tu correo electrónico</h4>
                    <p class="text-muted">
                        Antes de continuar, por favor verifica tu correo electrónico haciendo clic en el enlace que te enviamos.
                    </p>
                    <p class="text-muted mb-4">
                        Si no recibiste el correo, puedes solicitar uno nuevo.
                    </p>
                </div>

                {{-- Formulario para reenviar email --}}
                <form method="POST" action="{{ route('verification.resend') }}" class="mb-3">
                    @csrf
                    <button type="submit"
                            class="btn btn-lg btn-block btn-flat shadow-sm"
                            style="background: linear-gradient(135deg, #00794d 0%, #005235 100%);
                                    border: none; color: #fff;">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Reenviar correo de verificación
                    </button>
                </form>

                {{-- Botón para volver al login --}}
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('verificarLogin') }}"
                           class="btn btn-outline-secondary btn-block btn-flat">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al inicio de sesión
                        </a>
                    </div>
                </div>

                {{-- Información adicional --}}
                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted d-block text-center">
                        <i class="fas fa-question-circle mr-1"></i>
                        ¿Necesitas ayuda? Contacta al administrador del sistema.
                    </small>
                </div>
            </div>
        </div>
    </div>

    @push('css')
    <style>
        .login-box {
            width: 100%;
            max-width: 500px;
            margin: 5% auto;
        }

        .login-card-body {
            padding: 2rem;
        }

        .card-header {
            padding: 2rem 1.5rem;
            border-bottom: none;
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

        button[type="submit"] {
            transition: all 0.3s ease;
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, #008f5d 0%, #006644 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 121, 77, 0.3);
        }

        button[type="submit"]:active {
            transform: translateY(0);
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
@endsection

