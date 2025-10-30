@extends('adminlte::page')

@section('title', 'Ver Contrato/Convenio')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@section('content_header')
    <x-page-header
        icon="fas fa-eye"
        title="Ver Contrato/Convenio"
        subtitle="Información detallada del contrato o convenio"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Contratos y Convenios', 'url' => route('inventario.contratos-convenios.index')],
            ['label' => $contrato->name ?? 'N/A', 'active' => true]
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
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('inventario.contratos-convenios.index') }}">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>

            <div class="row">
                <!-- Estadísticas Generales -->
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $contrato->productos_count ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-boxes mr-1"></i>
                            Productos
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ ucfirst($contrato->tipo ?? 'N/A') }}</div>
                        <div class="stats-label">
                            <i class="fas fa-tag mr-1"></i>
                            Tipo
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $contrato->status == 1 ? 'Activo' : 'Inactivo' }}</div>
                        <div class="stats-label">
                            <i class="fas fa-toggle-on mr-1"></i>
                            Estado
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Contrato/Convenio -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información del Contrato/Convenio
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">
                                                <strong>{{ $contrato->name ?? 'N/A' }}</strong>
                                                <br><small class="text-muted">{{ ucfirst($contrato->tipo ?? 'N/A') }} registrado</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Código</th>
                                            <td class="py-3">{{ $contrato->codigo ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Proveedor</th>
                                            <td class="py-3">
                                                <i class="fas fa-truck mr-1"></i>
                                                {{ $contrato->proveedor->proveedor ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Valor</th>
                                            <td class="py-3">
                                                @if($contrato->valor)
                                                    <span class="status-badge status-active">
                                                        <i class="fas fa-dollar-sign mr-1"></i>
                                                        ${{ number_format($contrato->valor, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fechas</th>
                                            <td class="py-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Inicio:</strong><br>
                                                        @if($contrato->fecha_inicio)
                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                            @if(is_string($contrato->fecha_inicio))
                                                                {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}
                                                            @else
                                                                {{ $contrato->fecha_inicio->format('d/m/Y') }}
                                                            @endif
                                                        @else
                                                            <span class="text-muted">No especificada</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Fin:</strong><br>
                                                        @if($contrato->fecha_fin)
                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                            @php
                                                                $fechaFin = is_string($contrato->fecha_fin)
                                                                    ? \Carbon\Carbon::parse($contrato->fecha_fin)
                                                                    : $contrato->fecha_fin;
                                                            @endphp
                                                            <span class="badge badge-{{ $fechaFin->isPast() ? 'danger' : 'success' }}">
                                                                {{ $fechaFin->format('d/m/Y') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">Sin vigencia</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <x-status-badge
                                                    status="{{ $contrato->status ?? true }}"
                                                    activeText="ACTIVO"
                                                    inactiveText="INACTIVO"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Productos</th>
                                            <td class="py-3">
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-boxes mr-1"></i>
                                                    {{ $contrato->productos_count ?? 0 }} producto(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Creación</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($contrato->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última Actualización</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($contrato->updated_at)->format('d/m/Y H:i') }}
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
                                @can('EDITAR CONTRATO')
                                    <a href="{{ route('inventario.contratos-convenios.edit', $contrato->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan

                                @can('ELIMINAR CONTRATO')
                                    <form action="{{ route('inventario.contratos-convenios.destroy', $contrato->id) }}" 
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
    @vite(['resources/js/inventario/contratos_convenios.js'])
@endsection