@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-eye" 
        title="Detalles de Ficha de Caracterización"
        subtitle="Información detallada de la ficha"
        :breadcrumb="[['label' => 'Fichas de Caracterización', 'url' => route('fichaCaracterizacion.index') , 'icon' => 'fa-file-alt'], ['label' => 'Detalles', 'icon' => 'fa-eye', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('fichaCaracterizacion.index') }}">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>

            <div class="row">
                <div class="col-12">
                    <!-- Información Principal -->
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-file-alt mr-2"></i> Información General de la Ficha
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Número de Ficha</th>
                                            <td class="py-3">
                                                <span class="badge badge-primary badge-lg">{{ $ficha->ficha }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Programa de Formación</th>
                                            <td class="py-3">{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Sede</th>
                                            <td class="py-3">{{ $ficha->sede->sede ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Instructor Líder</th>
                                            <td class="py-3">
                                                @if($ficha->instructor)
                                                    <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                    {{ $ficha->instructor->persona->primer_nombre }} {{ $ficha->instructor->persona->primer_apellido }}
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Modalidad</th>
                                            <td class="py-3">{{ $ficha->modalidadFormacion->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Jornada</th>
                                            <td class="py-3">{{ $ficha->jornadaFormacion->jornada ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Ambiente</th>
                                            <td class="py-3">
                                                @if($ficha->ambiente)
                                                    <i class="fas fa-door-open mr-1"></i>
                                                    {{ $ficha->ambiente->title }} - {{ $ficha->ambiente->piso->nombre }}
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Horas</th>
                                            <td class="py-3">
                                                <i class="fas fa-hourglass-half mr-1"></i>
                                                {{ $ficha->total_horas ?? 0 }} horas
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $ficha->status ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $ficha->status ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Inicio</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $ficha->fecha_inicio ? \Carbon\Carbon::parse($ficha->fecha_inicio)->format('d/m/Y') : 'No definida' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Fin</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $ficha->fecha_fin ? \Carbon\Carbon::parse($ficha->fecha_fin)->format('d/m/Y') : 'No definida' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- Instructores Asignados -->
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-chalkboard-teacher mr-2"></i> Instructores Asignados
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($ficha->instructorFicha->count() > 0)
                                <div class="row">
                                    @foreach($ficha->instructorFicha as $asignacion)
                                        <div class="col-md-6 mb-3">
                                            <div class="card {{ $ficha->instructor_id == $asignacion->instructor_id ? 'border-primary' : 'border-secondary' }}">
                                                <div class="card-body">
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-user mr-1"></i>
                                                        {{ $asignacion->instructor->persona->primer_nombre }} 
                                                        {{ $asignacion->instructor->persona->segundo_nombre ?? '' }}
                                                        {{ $asignacion->instructor->persona->primer_apellido }}
                                                        {{ $asignacion->instructor->persona->segundo_apellido ?? '' }}
                                                        @if($ficha->instructor_id == $asignacion->instructor_id)
                                                            <span class="badge badge-primary ml-2">Principal</span>
                                                        @else
                                                            <span class="badge badge-secondary ml-2">Auxiliar</span>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($asignacion->fecha_inicio)->format('d/m/Y') }} - 
                                                        {{ \Carbon\Carbon::parse($asignacion->fecha_fin)->format('d/m/Y') }}
                                                    </p>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $asignacion->total_horas_instructor }} horas
                                                    </p>
                                                    @if($asignacion->instructorFichaDias && $asignacion->instructorFichaDias->count() > 0)
                                                        <p class="text-muted mb-0">
                                                            <i class="fas fa-calendar-week mr-1"></i>
                                                            <strong>Días:</strong> 
                                                            {{ $asignacion->instructorFichaDias->pluck('dia.name')->filter()->implode(', ') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-info-circle mr-1"></i> No hay instructores asignados a esta ficha.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Días de Formación -->
                    @if($ficha->diasFormacion->count() > 0)
                        <div class="card detail-card no-hover">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-week mr-2"></i> Días de Formación
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($ficha->diasFormacion as $diaFormacion)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body text-center">
                                                    <h6 class="mb-2">
                                                        <i class="fas fa-calendar-day mr-1"></i>
                                                        {{ $diaFormacion->dia->name }}
                                                    </h6>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $diaFormacion->hora_inicio }} - {{ $diaFormacion->hora_fin }}
                                                    </p>
                                                    <p class="text-muted mb-0">
                                                        <i class="fas fa-hourglass-half mr-1"></i>
                                                        {{ $diaFormacion->calcularHorasDia() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card detail-card no-hover">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-week mr-2"></i> Días de Formación
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    No hay días de formación configurados para esta ficha.
                                </div>
                            </div>
                        </div>
                    @endif


                    <!-- Aprendices Asignados -->
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-users mr-2"></i> Aprendices Asignados
                            </h5>
                            <div class="card-tools">
                                <span class="badge badge-info">{{ $ficha->aprendices->count() }} aprendices asignados</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($ficha->aprendices->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="15%">Documento</th>
                                                <th width="35%">Nombre Completo</th>
                                                <th width="25%">Email</th>
                                                <th width="15%">Teléfono</th>
                                                <th width="5%">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ficha->aprendices as $index => $aprendiz)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $aprendiz->persona->numero_documento }}</td>
                                                    <td>
                                                        <strong>
                                                            {{ $aprendiz->persona->primer_nombre }}
                                                            {{ $aprendiz->persona->segundo_nombre ?? '' }}
                                                            {{ $aprendiz->persona->primer_apellido }}
                                                            {{ $aprendiz->persona->segundo_apellido ?? '' }}
                                                        </strong>
                                                    </td>
                                                    <td>{{ $aprendiz->persona->email ?? 'N/A' }}</td>
                                                    <td>{{ $aprendiz->persona->telefono ?? $aprendiz->persona->celular ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($aprendiz->estado)
                                                            <span class="badge badge-success">Activo</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 text-muted text-center">
                                    <small><strong>Total:</strong> {{ $ficha->aprendices->count() }} aprendices asignados</small>
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-info-circle mr-1"></i> No hay aprendices asignados a esta ficha.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/detalle-generico.js'])
@endsection
