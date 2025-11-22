@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-eye" 
        title="Asignaciones de instructores"
        subtitle="Detalle de la asignación"
        :breadcrumb="[
            ['label' => 'Asignaciones', 'url' => route('asignaciones.instructores.index'), 'icon' => 'fa-user-check'],
            ['label' => 'Detalle de la asignación', 'icon' => 'fa-eye', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('asignaciones.instructores.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al listado
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle de la asignación
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Ficha</th>
                                            <td class="py-3">
                                                <span class="badge badge-primary mr-2">
                                                    {{ $asignacion->ficha->ficha ?? 'N/A' }}
                                                </span>
                                                {{ $asignacion->ficha->programaFormacion->nombre ?? 'Programa no disponible' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Instructor</th>
                                            <td class="py-3">
                                                @if($asignacion->instructor && $asignacion->instructor->persona)
                                                    <div class="d-flex flex-column">
                                                        <span class="font-weight-bold">
                                                            {{ $asignacion->instructor->persona->primer_nombre }}
                                                            {{ $asignacion->instructor->persona->segundo_nombre }}
                                                            {{ $asignacion->instructor->persona->primer_apellido }}
                                                            {{ $asignacion->instructor->persona->segundo_apellido }}
                                                        </span>
                                                        <small class="text-muted">
                                                            Documento: {{ $asignacion->instructor->persona->numero_documento }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Instructor no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Competencia</th>
                                            <td class="py-3">
                                                <span class="badge badge-info mr-2">
                                                    {{ $asignacion->competencia->codigo ?? 'N/A' }}
                                                </span>
                                                {{ $asignacion->competencia->nombre ?? 'Competencia no disponible' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Resultados de aprendizaje</th>
                                            <td class="py-3">
                                                @if($asignacion->resultadosAprendizaje->isEmpty())
                                                    <span class="text-muted">No se han relacionado resultados de aprendizaje.</span>
                                                @else
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($asignacion->resultadosAprendizaje as $resultado)
                                                            <li class="mb-1">
                                                                <span class="badge badge-secondary mr-2">{{ $resultado->codigo }}</span>
                                                                {{ $resultado->nombre }}
                                                                @if ($resultado->duracion)
                                                                    <small class="text-muted">({{ $resultado->duracion }} h)</small>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Creada</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ optional($asignacion->created_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Actualizada</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ optional($asignacion->updated_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('asignaciones.instructores.edit', $asignacion) }}" class="btn btn-outline-primary btn-sm mx-1">
                                    <i class="fas fa-pencil-alt mr-1"></i> Editar asignación
                                </a>
                                <a href="{{ route('asignaciones.instructores.index') }}" class="btn btn-outline-secondary btn-sm mx-1">
                                    <i class="fas fa-list mr-1"></i> Ver todas las asignaciones
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

