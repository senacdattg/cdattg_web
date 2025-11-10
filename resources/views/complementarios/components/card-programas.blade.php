@props(['programa', 'programasInscritosIds' => collect()])

@php
    $estaInscrito = $programasInscritosIds->contains($programa->id);
@endphp

<div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-4">
    <div class="card h-100 shadow-sm">
        <div class="card-body text-center py-3">
            <div class="mb-3">
                <i class="{{ $programa->icono }} fa-3x text-primary"></i>
            </div>
            <h6 class="card-title font-weight-bold mb-2 text-center">{{ $programa->nombre }}</h6>

            <p class="card-text text-muted small mb-3">{{ $programa->descripcion }}</p>
            <div class="mt-3 pt-2 border-top">
                <small class="text-muted">Duración</small>
                <p class="mb-0 font-weight-bold small">{{ formatear_horas($programa->duracion) }} horas</p>
            </div>
        </div>
        <div class="card-footer bg-transparent py-2">
            <div class="d-grid gap-2 text-center">
                @if($estaInscrito)
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                        <i class="fas fa-check-circle mr-1"></i> Ya Inscrito
                    </button>
                @else
                    <a href="{{ route('programa_complementario.ver', ['id' => $programa->id]) }}"
                        class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye mr-1"></i> Ver Detalles
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
