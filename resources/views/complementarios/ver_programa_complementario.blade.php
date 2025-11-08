@extends('adminlte::page')

@section('title', (is_object($programa) ? ($programa->nombre ?? 'Programa') : (string)($programa ?? 'Programa')))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-graduation-cap me-2"></i>
                {{ is_object($programa) ? ($programa->nombre ?? 'Programa') : (string)($programa ?? 'Programa') }}
            </h1>
            <p class="text-muted mb-0">Detalle del programa complementario</p>
        </div>
        <div>
            <a href="{{ route('gestion-programas-complementarios') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Volver a Gestión
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="h1 text-primary mb-2">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <h2 class="mb-1">
                                {{ is_object($programa) ? ($programa->nombre ?? 'Nombre no disponible') :
                                    (string)($programa ?? 'Nombre no disponible') }}
                            </h2>
                            @if(is_object($programa) && isset($programa->estado))
                                <span class="badge bg-{{ $programa->estado == 'activo' ? 'success' :
                                    'secondary' }} mb-2">
                                    {{ ucfirst($programa->estado) }}
                                </span>
                            @endif
                        </div>

                        <h5>Descripción</h5>
                        <p class="text-muted">
                            {!! is_object($programa) ? ($programa->descripcion ?? 'No hay descripción disponible.') :
                                'No hay descripción disponible.' !!}
                        </p>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-2">
                                <h6 class="mb-1">Código</h6>
                                <p class="mb-0"><strong>{{ is_object($programa) ? ($programa->codigo ?? '-') :
                                    '-' }}</strong></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <h6 class="mb-1">Duración</h6>
                                <p class="mb-0"><strong>{{ is_object($programa) ? ($programa->duracion ?? '—') :
                                    '—' }}</strong></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <h6 class="mb-1">Modalidad</h6>
                                <p class="mb-0"><strong>{{ is_object($programa) ? ($programa->modalidad ?? '—') :
                                    '—' }}</strong></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <h6 class="mb-1">Jornada</h6>
                                <p class="mb-0"><strong>{{ is_object($programa) ? ($programa->jornada ?? '—') :
                                    '—' }}</strong></p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6>Requisitos</h6>
                            @if(is_object($programa) && !empty($programa->requisitos))
                                <ul class="list-group list-group-flush">
                                    @foreach(explode("\n", $programa->requisitos) as $req)
                                        @if(trim($req))
                                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>
                                                {{ trim($req) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted small mb-0">No hay requisitos registrados.</p>
                            @endif
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            @php
                                $programParam = is_object($programa) ? ($programa->id ?? $programa) : $programa;
                            @endphp
                            <a href="{{ route('programa_complementario.ver', ['programa' => $programParam]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Ver en público
                            </a>
                            <a href="#" class="btn btn-outline-warning"><i class="fas fa-edit me-1"></i> Editar</a>
                            <a href="#" class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i> Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top shadow-sm" style="top:20px;">
                    <div class="card-body text-center">
                        <h5>Inscripción</h5>
                        <p class="small text-muted">Si estás interesado, realiza tu inscripción.</p>
                        <a href="#" class="btn btn-success btn-lg w-100 mb-2">
                            <i class="fas fa-clipboard-list me-1"></i> Inscripción Rápida
                        </a>
                        <a href="mailto:info@sena.edu.co" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-envelope me-1"></i> Consultar
                        </a>

                        <hr>

                        <div class="text-start mt-3">
                            <h6>Información adicional</h6>
                            <p class="small text-muted mb-1"><strong>Sede:</strong>
                                {{ is_object($programa) ? ($programa->sede->nombre ?? '-') : '-' }}</p>
                            <p class="small text-muted mb-1"><strong>Nivel:</strong>
                                {{ is_object($programa) ? ($programa->nivel_formacion_id ?? '-') : '-' }}</p>
                            <p class="small text-muted mb-0"><strong>Última actualización:</strong>
                                {{ is_object($programa) && isset($programa->updated_at) ?
                                    $programa->updated_at->format('Y-m-d') : '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    {{-- Si necesitas estilos específicos para esta vista puedes añadirlos aquí --}}
    <style>
        .program-icon { font-size: 3rem; color: #094577; }
        .card h2 { font-weight: 700; }
    </style>
@stop

@section('js')
    <script>
        console.log('Vista detalle programa complementario cargada');
    </script>
@stop