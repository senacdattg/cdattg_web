@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-user-graduate" 
        title="Aprendiz"
        subtitle="Detalles del aprendiz"
        :breadcrumb="[['label' => 'Aprendices', 'url' => '{{ route('aprendices.index') }}', 'icon' => 'fa-user-graduate'], ['label' => 'Detalles del aprendiz', 'icon' => 'fa-info-circle', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('aprendices.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información del Aprendiz
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre Completo</th>
                                            <td class="py-3">{{ $aprendiz->persona?->nombre_completo ?? 'Sin información' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Tipo de Documento</th>
                                            <td class="py-3">{{ $aprendiz->persona?->tipoDocumento?->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Número de Documento</th>
                                            <td class="py-3">{{ $aprendiz->persona?->numero_documento ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Correo Electrónico</th>
                                            <td class="py-3">
                                                @if($aprendiz->persona?->email)
                                                    <a href="mailto:{{ $aprendiz->persona->email }}">
                                                        <i class="fas fa-envelope mr-1"></i>
                                                        {{ $aprendiz->persona->email }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Celular</th>
                                            <td class="py-3">
                                                @if($aprendiz->persona?->celular)
                                                    <a href="https://wa.me/{{ $aprendiz->persona->celular }}" target="_blank">
                                                        <i class="fab fa-whatsapp text-success mr-1"></i>
                                                        {{ $aprendiz->persona->celular }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Ficha de Caracterización</th>
                                            <td class="py-3">
                                                @if($aprendiz->fichaCaracterizacion)
                                                    <span class="badge badge-info">{{ $aprendiz->fichaCaracterizacion->ficha }}</span>
                                                    - {{ $aprendiz->fichaCaracterizacion->programaFormacion->nombre ?? 'N/A' }}
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Programa de Formación</th>
                                            <td class="py-3">{{ $aprendiz->fichaCaracterizacion?->programaFormacion?->nombre ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Jornada</th>
                                            <td class="py-3">{{ $aprendiz->fichaCaracterizacion?->jornadaFormacion?->jornada ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha Inicio de Formación</th>
                                            <td class="py-3 timestamp">
                                                @if($aprendiz->fichaCaracterizacion?->fecha_inicio)
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($aprendiz->fichaCaracterizacion->fecha_inicio)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha Fin de Formación</th>
                                            <td class="py-3 timestamp">
                                                @if($aprendiz->fichaCaracterizacion?->fecha_fin)
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($aprendiz->fichaCaracterizacion->fecha_fin)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Asistencias</th>
                                            <td class="py-3">
                                                <span class="badge badge-success">{{ $aprendiz->asistencias->count() }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $aprendiz->estado ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $aprendiz->estado ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Registro</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $aprendiz->created_at->format('d/m/Y H:i:s') }}
                                                <span class="text-muted">({{ $aprendiz->created_at->diffForHumans() }})</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última Actualización</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $aprendiz->updated_at->format('d/m/Y H:i:s') }}
                                                <span class="text-muted">({{ $aprendiz->updated_at->diffForHumans() }})</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR APRENDIZ')
                                    <form action="{{ route('aprendices.cambiarEstado', $aprendiz->id) }}"
                                        method="POST" class="d-inline mx-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-sync mr-1"></i> Cambiar Estado
                                        </button>
                                    </form>
                                    @if(isset($aprendiz) && $aprendiz->id)
                                        <a href="{{ route('aprendices.edit', $aprendiz->id) }}"
                                            class="btn btn-outline-info btn-sm mx-1">
                                            <i class="fas fa-pencil-alt mr-1"></i> Editar
                                        </a>
                                    @endif
                                @endcan
                                @can('ELIMINAR APRENDIZ')
                                    <form action="{{ route('aprendices.destroy', $aprendiz->id) }}"
                                        method="POST" class="d-inline mx-1 formulario-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
    @include('layout.alertas')
@endsection
