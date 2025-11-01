@extends('complementarios.layout.master-layout-complementarios')
@section('title', 'Programas Complementarios | SENA')
@section('css')
    @vite(['resources/css/programas_publicos.css'])
@endsection
@section('content')
    

    <div class="container-fluid mt-4 px-2 px-md-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center mb-4">
                    <h2 class="text-success">Programas Complementarios</h2>
                    <p class="text-muted">Descubre nuestros programas de formación complementaria disponibles</p>
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

                        <!-- Programs Cards View -->
                        <div class="row justify-content-center g-3">
                            @foreach ($programas as $programa)
                                <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-4">
                                    <div class="card card-outline card-success h-100 shadow-sm">
                                        <div class="card-body text-center py-3">
                                            <div class="mb-3">
                                                <i class="{{ $programa->icono }} fa-3x text-success"></i>
                                            </div>
                                            <h6 class="card-title font-weight-bold mb-2 text-center">{{ $programa->nombre }}</h6>
                                            
                                            <p class="card-text text-muted small mb-3">{{ $programa->descripcion }}</p>
                                            <div class="mt-3 pt-2 border-top">
                                                <small class="text-muted">Duración</small>
                                                <p class="mb-0 font-weight-bold small">{{ $programa->duracion }} horas</p>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent py-2">
                                            <div class="d-grid gap-2 text-center">
                                                <a href="{{ route('programa_complementario.ver', ['id' => $programa->id]) }}"
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-eye mr-1"></i> Ver Detalles
                                                </a>
                                                
                                            </div>
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
