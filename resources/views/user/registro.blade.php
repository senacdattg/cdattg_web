@extends('layout.master-layout-registro')

@push('styles')
    @vite('resources/css/pages/registro.css')
@endpush

@section('content')
    <div class="register-box">
        <div class="card sena-card shadow-lg">
            <div class="card-header sena-header text-center text-white">
                <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle sena-header-icon d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                    <h1 class="h4 mb-1 font-weight-bold">Crea tu cuenta en Académica</h1>
                    <span class="small" style="color: rgba(255, 255, 255, 0.75);">
                        Conéctate con los programas complementarios del SENA.
                    </span>
                </div>
            </div>
            <div class="card-body sena-body">
                <div class="alert sena-alert border-0 d-flex align-items-start">
                    <i class="fas fa-info-circle fa-lg mr-3 mt-1 sena-alert__icon"></i>
                    <div>
                        <strong class="d-block mb-1 sena-alert__title">¿Por qué pedimos estos datos?</strong>
                        <p class="mb-0 small text-muted">
                            Validamos tu identidad y aseguramos la comunicación.
                            <br>
                            Personalizamos tu experiencia en el portal.
                        </p>
                    </div>
                </div>

                <form id="registroForm" action="{{ route('registrarme') }}" method="post" class="needs-validation"
                    novalidate>
                    @csrf

                    @include('personas.partials.form', [
                        'persona' => null,
                        'documentos' => $documentos,
                        'generos' => $generos,
                        'caracterizaciones' => $caracterizaciones,
                        'paises' => $paises,
                        'departamentos' => $departamentos,
                        'municipios' => $municipios,
                        'cardinales' => $cardinales,
                        'showCaracterizacion' => true,
                    ])

                    <div class="row mt-4">
                        <div class="col-md-6 mb-2">
                            <button type="submit"
                                class="btn btn-block py-2 text-uppercase font-weight-semibold sena-btn-primary">
                                <i class="fas fa-user-plus mr-2"></i>Registrarme ahora
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('/') }}"
                                class="btn btn-block py-2 text-uppercase font-weight-semibold sena-btn-outline">
                                <i class="fas fa-arrow-left mr-2"></i>Volver al inicio
                            </a>
                        </div>
                    </div>
                    <p class="text-muted text-center small mb-0 mt-3">
                        Al continuar aceptas el tratamiento seguro de tus datos.
                        <br>
                        Consulta la política de privacidad del SENA para más información.
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection
