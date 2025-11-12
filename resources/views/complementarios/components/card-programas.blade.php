@props(['programa'])

@php
    $modalidad = $programa->modalidad_nombre ?? optional($programa->modalidad->parametro)->name;
    $jornada = $programa->jornada_nombre ?? optional($programa->jornada)->jornada;
    $estadoBadge = $programa->estado_label ?? 'Disponible';
    $badgeClass = $programa->badge_class ?? 'bg-success';
    $slugModalidad = \Illuminate\Support\Str::slug($modalidad ?? 'todas');
    $slugJornada = \Illuminate\Support\Str::slug($jornada ?? 'todas');
    $slugEstado = \Illuminate\Support\Str::slug($estadoBadge);
    $searchSegments = [$programa->nombre ?? '', $modalidad ?? '', $jornada ?? '', $programa->descripcion ?? ''];
    $searchTokens = \Illuminate\Support\Str::of(implode(' ', array_filter($searchSegments)))
        ->lower()
        ->value();
@endphp

<div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-4 program-card"
    data-name="{{ \Illuminate\Support\Str::of($programa->nombre ?? '')->lower() }}" data-modalidad="{{ $slugModalidad }}"
    data-jornada="{{ $slugJornada }}" data-estado="{{ $slugEstado }}" data-search="{{ $searchTokens }}">
    <div class="card h-100 shadow-sm border-0">
        <div class="card-body d-flex flex-column justify-content-between py-4 px-4">
            <div class="text-center mb-3">
                <span class="badge badge-pill {{ $badgeClass }} px-3 py-2 text-uppercase small">
                    <i class="fas fa-circle mr-1"></i> {{ $estadoBadge }}
                </span>
            </div>

            <div class="text-center">
                <div class="mb-3">
                    <i class="{{ $programa->icono }} fa-3x text-primary"></i>
                </div>
                <h5 class="font-weight-bold mb-2 text-dark">{{ $programa->nombre }}</h5>
                <p class="text-muted small mb-3">{{ \Illuminate\Support\Str::limit($programa->descripcion, 110) }}</p>
            </div>

            <div class="border-top pt-3">
                <div class="row text-muted small">
                    <div class="col-6 border-right">
                        <i class="fas fa-clock text-primary mr-1"></i>
                        {{ formatear_horas($programa->duracion) }} h
                    </div>
                    <div class="col-6">
                        <i class="fas fa-users text-primary mr-1"></i>
                        {{ $programa->cupos }} cupos
                    </div>
                </div>
                @if ($modalidad || $jornada)
                    <div class="mt-2 small text-muted">
                        @if ($modalidad)
                            <div>
                                <i class="fas fa-chalkboard-teacher text-primary mr-1"></i>
                                {{ $modalidad }}
                            </div>
                        @endif
                        @if ($jornada)
                            <div>
                                <i class="fas fa-sun text-primary mr-1"></i>
                                {{ $jornada }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        <div class="card-footer bg-white border-0 pt-0 pb-4">
            <a href="{{ route('programa_complementario.ver', ['id' => $programa->id]) }}"
                class="btn btn-outline-primary btn-sm btn-block">
                <i class="fas fa-eye mr-1"></i> Ver detalles
            </a>
        </div>
    </div>
</div>
