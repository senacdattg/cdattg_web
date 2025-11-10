@props(['programa'])

<div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-4">
    <div class="card h-100 shadow-sm">
        <div class="card-body text-center py-3">
            <h6 class="card-title font-weight-bold mb-2 text-center">{{ $programa->nombre }}</h6>
            <p class="card-text text-muted small mb-3">{{ $programa->descripcion }}</p>
            <div class="mt-3 pt-2 border-top">
                <small class="text-muted">Duración</small>
                <p class="mb-0 font-weight-bold small">{{ formatear_horas($programa->duracion) }} horas</p>
            </div>
        </div>
    </div>
</div>
