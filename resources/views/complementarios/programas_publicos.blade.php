@extends('layout.master-layout-registro')
@section('content')
<div class="card card-primary">
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
            @foreach($programas as $programa)
            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <div class="card card-outline card-info h-100 shadow-sm">
                    <div class="card-body text-center py-3">
                        <div class="mb-3">
                            <i class="{{ $programa->icono }} fa-3x text-primary"></i>
                        </div>
                        <h6 class="card-title font-weight-bold mb-2">{{ $programa->nombre }}</h6>
                        <span class="badge badge-success mb-3">Con Oferta</span>
                        <p class="card-text text-muted small mb-3">{{ $programa->descripcion }}</p>
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted">Duración</small>
                            <p class="mb-0 font-weight-bold small">{{ $programa->duracion }} horas</p>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent py-2">
                        <a href="{{ route('programa_complementario.ver', ['id' => $programa->id]) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-eye mr-1"></i> Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection