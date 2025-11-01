@props(['programaData'])

<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-graduation-cap mr-2"></i>{{ $programaData['nombre'] }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('programas-complementarios.publicos') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Volver a Programas
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="text-center mb-5">
                    <i class="{{ $programaData['icono'] }} fa-5x text-success mb-4"></i>
                    <h2 class="mb-3">{{ $programaData['nombre'] }}</h2>
                    <span class="badge badge-success">Con Oferta</span>
                </div>

                <h5 class="mb-3">Descripción</h5>
                <p class="text-muted mb-5">{{ $programaData['descripcion'] }}</p>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <div class="info-box-content py-3">
                                <span class="info-box-text">Duración</span>
                                <span class="info-box-number">{{ $programaData['duracion'] }} horas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-widget widget-user">
                    <div class="widget-user-header bg-success">
                        <h3 class="widget-user-username">Inscripción</h3>
                        <h5 class="widget-user-desc">Programa Disponible</h5>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="description-block">
                                    <span class="description-text">¿ESTÁS INTERESADO?</span>
                                    <p class="text-muted mb-3">Realiza tu inscripción ahora mismo</p>
                                    @auth
                                    <a href="{{ route('programas-complementarios.inscripcion', ['id' => $programaData['id']]) }}"
                                        class="btn btn-success btn-block">
                                        <i class="fas fa-user-plus mr-1"></i> Inscribirse
                                    </a>
                                    @else
                                    <button type="button" class="btn btn-success btn-block"
                                            onclick="openInscripcionModal({{ $programaData['id'] }}, '{{ $programaData['nombre'] }}')">
                                        <i class="fas fa-user-plus mr-1"></i> Inscribirse
                                    </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>