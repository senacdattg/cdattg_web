@extends('complementarios.layout.master-layout-complementarios')
@section('title', 'Programas Complementarios | SENA')

@section('content')
    <style>
        /* Hero Banner */
        .hero-banner {
            position: relative;
            overflow: hidden;
            background: linear-gradient(120deg, #5b86e5 0%, #36d1dc 100%);
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
            background: radial-gradient(circle at center, rgba(255,255,255,0.18), rgba(255,255,255,0) 60%);
            transform: rotate(18deg);
        }
        .hero-banner::before {
            width: 40vw;
            height: 40vw;
            left: -15vw;
            bottom: -10vw;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1), rgba(255,255,255,0) 60%);
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
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.35);
            color: #fff;
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
            background: #f8fff9;
            border-bottom: 1px solid #e6f3ea;
        }
        .programs-card .badge-success {
            background: #28a745;
        }
    </style>

    <div class="container-fluid mt-3 px-2 px-md-4">
        <!-- Hero Banner -->
        <section class="hero-banner rounded shadow-lg mb-4">
            <div class="container py-5">
                <div class="row align-items-center">
                    <div class="col-12 col-md-5 text-center mb-4 mb-md-0">
                        <img src="{{ asset('img/flor_guaviare.png') }}" class="img-fluid hero-figure" alt="Imagen representativa SENA">
                    </div>
                    <div class="col-12 col-md-7 text-white">
                        <span class="badge hero-badge mb-2">Formación Complementaria</span>
                        <h1 class="display-4 font-weight-bold mb-2">Programas Complementarios</h1>
                        <p class="lead mb-0">Descubre nuestros programas de formación complementaria disponibles</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programas -->
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-10">

                <div class="card programs-card card-success border-0 shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-graduation-cap mr-2"></i>Programas Complementarios
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-success">Disponibles</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center g-3">
                            @foreach ($programas as $programa)
                                @include('complementarios.components.card-programas', ['programa' => $programa])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layout.footer')
@endsection
