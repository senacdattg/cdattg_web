@extends('adminlte::page')

@section('title', 'Ver Marca')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-eye"
        title="Ver Marca"
        subtitle="Información detallada de la marca"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Marcas', 'url' => route('inventario.marcas.index')],
            ['label' => $marca->name, 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Alertas -->
            @include('components.session-alerts')

            <!-- Botón Volver -->
            <div class="mb-3">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('inventario.marcas.index') }}">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>

            <div class="row">
                <!-- Estadísticas Generales -->
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $marca->productos_count ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-boxes mr-1"></i>
                            Productos
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $marca->status == 1 ? 'Activa' : 'Inactiva' }}</div>
                        <div class="stats-label">
                            <i class="fas fa-toggle-on mr-1"></i>
                            Estado
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ \Carbon\Carbon::parse($marca->created_at)->diffForHumans() }}</div>
                        <div class="stats-label">
                            <i class="fas fa-clock mr-1"></i>
                            Antigüedad
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Marca -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información de la Marca
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">
                                                <strong>{{ $marca->name }}</strong>
                                                <br><small class="text-muted">Marca registrada</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <x-status-badge
                                                    status="{{ $marca->status ?? true }}"
                                                    activeText="ACTIVA"
                                                    inactiveText="INACTIVA"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Descripción</th>
                                            <td class="py-3">{{ $marca->descripcion ?? 'Sin descripción' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Productos</th>
                                            <td class="py-3">
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-boxes mr-1"></i>
                                                    {{ $marca->productos_count ?? 0 }} producto(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Creación</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($marca->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última Actualización</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($marca->updated_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-footer bg-white py-3">
                            <div class="action-buttons">
                                @can('EDITAR MARCA')
                                    <a href="{{ route('inventario.marcas.edit', $marca->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan

                                @can('ELIMINAR MARCA')
                                    <form action="{{ route('inventario.marcas.destroy', $marca->id) }}"
                                          method="POST" class="d-inline formulario-eliminar">
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
@endsection

@section('js')
    @vite(['resources/js/inventario/marcas.js'])
@endsection