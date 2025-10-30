@extends('layout.master-layout-registro')
@extends('layout.alertas')
@section('css')
    @vite(['resources/css/programas_publicos.css'])
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-xl-10">
                <div class="text-center mb-4">
                    <img src="{{ asset('vendor/adminlte/dist/img/LogoSena.png') }}" alt="SENA Logo" class="mb-3" style="height: 80px;">
                    <h2 class="text-success">Servicio Nacional de Aprendizaje - SENA</h2>
                    <p class="text-muted">Regional Guaviare</p>
                </div>
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-graduation-cap mr-2"></i>Programas Complementarios
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-success">Disponibles</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Descubre nuestros programas de formación complementaria disponibles</p>

                        <!-- Programs Cards View -->
                        <div class="row justify-content-center">
                            @foreach ($programas as $programa)
                                <div class="col-12 col-md-6 col-lg-4 mb-4">
                                    <div class="card card-outline card-success h-100 shadow-sm">
                                        <div class="card-body text-center py-3">
                                            <div class="mb-3">
                                                <i class="{{ $programa->icono }} fa-3x text-success"></i>
                                            </div>
                                            <h6 class="card-title font-weight-bold mb-2">{{ $programa->nombre }}</h6>
                                            <div class="mb-3">
                                                <span class="badge badge-success">Con Oferta</span>
                                            </div>
                                            <p class="card-text text-muted small mb-3">{{ $programa->descripcion }}</p>
                                            <div class="mt-3 pt-2 border-top">
                                                <small class="text-muted">Duración</small>
                                                <p class="mb-0 font-weight-bold small">{{ $programa->duracion }} horas</p>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent py-2">
                                            <a href="{{ route('programa_complementario.ver', ['id' => $programa->id]) }}"
                                                class="btn btn-success btn-block">
                                                <i class="fas fa-eye mr-1"></i> Ver Detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layout.footer')
@endsection
