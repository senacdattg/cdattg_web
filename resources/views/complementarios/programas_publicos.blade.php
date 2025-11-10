@extends('complementarios.layout.master-layout-complementarios')
@section('title', 'Programas Complementarios | SENA')

@section('content')
    <style>
        /* Hero Banner */
        .hero-banner {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #ebf1f4 100%);
            border: 1px solid #dee2e6;
        }
        .hero-banner::after,
        .hero-banner::before {
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-banner::after {
            width: 60vw;
            height: 60vw;
            right: -20vw;
            top: -15vw;
            background: radial-gradient(circle at center, rgba(255,255,255,0.12), rgba(255,255,255,0) 60%);
            transform: rotate(18deg);
        }
        .hero-banner::before {
            width: 40vw;
            height: 40vw;
            left: -15vw;
            bottom: -10vw;
            background: radial-gradient(circle at center, rgba(255,255,255,0.06), rgba(255,255,255,0) 60%);
        }
        .hero-figure {
            max-height: 320px;
            width: auto;
            filter: drop-shadow(0 12px 24px rgba(0,0,0,0.25));
        }
        .hero-emphasis {
            font-size: 1.15em;
            text-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        }
        .hero-badge {
            background: #ffffff;
            border: 1px solid #007bff;
            color: #007bff;
        }
        @media (max-width: 767.98px) {
            .hero-figure {
                max-height: 200px;
            }
        }

        /* Section heading styles */
        .section-heading h2 {
            letter-spacing: .3px;
        }

        /* Card tweaks for a cleaner look */
        .programs-card .card-header {
            background: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }
        .programs-card .badge-success {
            background: #007bff;
        }
    </style>

    <div class="container-fluid mt-3 px-2 px-md-4" style="background-color: #ebf1f4; min-height: 100vh;">
        <!-- Hero Banner -->
        <section class="hero-banner rounded shadow-lg mb-4">
            <div class="container py-5">
                <div class="row align-items-center">
                    <div class="col-12 col-md-5 text-center mb-4 mb-md-0">
                        <img src="{{ asset('img/flor_guaviare.png') }}" class="img-fluid hero-figure"
                            alt="Imagen representativa SENA">
                    </div>
                    <div class="col-12 col-md-7 text-dark">
                        <span class="badge hero-badge mb-2">Formación Complementaria</span>
                        <h1 class="display-4 font-weight-bold mb-2">Programas Complementarios</h1>
                        <p class="lead mb-0">Descubre nuestros programas de formación complementaria disponibles</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Alertas de sesión -->
        <div class="container-fluid px-2 px-md-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-10">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert"
                            style="margin-top: 20px;">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>¡Éxito!</strong> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert"
                            style="margin-top: 20px;">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Error:</strong> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert"
                            style="margin-top: 20px;">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Advertencia:</strong> {{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert"
                            style="margin-top: 20px;">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Información:</strong> {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Programas -->
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-10">

                <div class="card programs-card border-0 shadow-sm"
                    style="background-color: #ffffff; border-color: #dee2e6;">
                    <div class="card-header d-flex align-items-center justify-content-between"
                        style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-graduation-cap mr-2"></i>Programas Complementarios
                        </h3>
                        <div class="card-tools">
                            <span class="badge" style="background-color: #ffffff; color: #007bff;">Disponibles</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center g-3">
                            @foreach ($programas as $programa)
                                @include('complementarios.components.card-programas', [
                                    'programa' => $programa,
                                    'programasInscritosIds' => $programasInscritosIds ?? collect()
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('complementarios.layout.footer-complementarios')
@endsection
