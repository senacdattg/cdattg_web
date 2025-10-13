@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-graduation-cap" 
        title="Programa de Formación"
        subtitle="Detalles del programa de formación"
        :breadcrumb="[['label' => 'Programas de Formación', 'url' => '{{ route('programa.index') }}', 'icon' => 'fa-graduation-cap'], ['label' => 'Detalles del programa', 'icon' => 'fa-info-circle', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('programa.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle del Programa de Formación
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">ID</th>
                                            <td class="py-3">{{ $programa->id }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Código</th>
                                            <td class="py-3">
                                                <span class="badge badge-secondary">{{ $programa->codigo }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3 font-weight-medium">{{ $programa->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Red de Conocimiento</th>
                                            <td class="py-3">
                                                @if ($programa->redConocimiento)
                                                    <span class="text-primary">
                                                        <i class="fas fa-network-wired mr-1"></i>
                                                        {{ $programa->redConocimiento->nombre }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Nivel de Formación</th>
                                            <td class="py-3">
                                                @if ($programa->nivelFormacion)
                                                    <span class="text-success">
                                                        <i class="fas fa-layer-group mr-1"></i>
                                                        {{ $programa->nivelFormacion->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $programa->status ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $programa->status ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que crea</th>
                                            <td class="py-3 user-info">
                                                @if ($programa->userCreated)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $programa->userCreated->persona->primer_nombre ?? 'Usuario' }}
                                                    {{ $programa->userCreated->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que edita</th>
                                            <td class="py-3 user-info">
                                                @if ($programa->userEdited)
                                                    <i class="fas fa-user-edit mr-1"></i>
                                                    {{ $programa->userEdited->persona->primer_nombre ?? 'Usuario' }}
                                                    {{ $programa->userEdited->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de creación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $programa->created_at->diffForHumans() }}
                                                <small class="text-muted d-block">{{ $programa->created_at->format('d/m/Y H:i:s') }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última actualización</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $programa->updated_at->diffForHumans() }}
                                                <small class="text-muted d-block">{{ $programa->updated_at->format('d/m/Y H:i:s') }}</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="row">
                                <div class="col-md-6">
                                    @can('programa.edit')
                                        <a href="{{ route('programa.edit', $programa->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit mr-1"></i>Editar Programa
                                        </a>
                                    @endcan
                                </div>
                                <div class="col-md-6 text-right">
                                    @can('programa.delete')
                                        <form action="{{ route('programa.destroy', $programa->id) }}" method="POST" class="d-inline formulario-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash mr-1"></i>Eliminar Programa
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('components.confirm-delete-modal')
@endsection

@section('js')
    @vite(['resources/js/pages/detalle-generico.js'])
@endsection