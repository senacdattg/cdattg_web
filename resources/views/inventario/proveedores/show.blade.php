@extends('adminlte::page')

@section('title', 'Ver Proveedor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['public/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-eye"
        title="Ver Proveedor"
        subtitle="Información detallada del proveedor"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Proveedores', 'url' => route('inventario.proveedores.index')],
            ['label' => $proveedor->proveedor, 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Alertas -->
            @include('components.session-alerts')

            <!-- Botón Volver -->
            <div class="mb-3" id="boton-volver">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('inventario.proveedores.index') }}">
                    <i class="fas fa-arrow-left mr-1" id="icono-volver"></i> Volver
                </a>
            </div>

            <div class="row" id="estadisticas-generales">
                <!-- Estadísticas Generales -->
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $proveedor->contratos_convenios_count ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-file-contract mr-1"></i>
                            Contratos
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $proveedor->productos_count ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-boxes mr-1"></i>
                            Productos
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">
                            {{ $proveedor->estado->parametro->name ?? 'SIN ESTADO' }}
                        </div>
                        <div class="stats-label">
                            <i class="fas fa-toggle-on mr-1"></i>
                            Estado
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Proveedor -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div id="informacion-proveedor" class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información del Proveedor
                            </h5>
                        </div>

                        <div class="card-body p-0" id="detalle">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre del Proveedor</th>
                                            <td class="py-3">
                                                <strong>{{ $proveedor->proveedor }}</strong>
                                                <br><small class="text-muted">Proveedor registrado</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">NIT</th>
                                            <td class="py-3">
                                                <i class="fas fa-id-card mr-1"></i>
                                                {{ $proveedor->nit ?? 'No especificado' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Correo Electrónico</th>
                                            <td class="py-3">
                                                @if($proveedor->email)
                                                    <a href="mailto:{{ $proveedor->email }}" class="text-primary">
                                                        <i class="fas fa-envelope mr-1"></i>
                                                        {{ $proveedor->email }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Teléfono</th>
                                            <td class="py-3">
                                                @if($proveedor->telefono)
                                                    <a href="tel:{{ $proveedor->telefono }}" class="text-primary">
                                                        <i class="fas fa-phone mr-1"></i>
                                                        {{ $proveedor->telefono }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Dirección</th>
                                            <td class="py-3">
                                                @if($proveedor->direccion)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $proveedor->direccion }}
                                                @else
                                                    <span class="text-muted">No especificada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Departamento</th>
                                            <td class="py-3">
                                                @if($proveedor->departamento)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $proveedor->departamento->departamento }}
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Municipio</th>
                                            <td class="py-3">
                                                @if($proveedor->municipio)
                                                    <i class="fas fa-map-pin mr-1"></i>
                                                    {{ $proveedor->municipio->municipio }}
                                                    <small class="text-muted">({{ $proveedor->municipio->departamento->departamento ?? 'N/A' }})</small>
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Contacto</th>
                                            <td class="py-3">
                                                @if($proveedor->contacto)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $proveedor->contacto }}
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                @if($proveedor->estado)
                                                    <span class="badge badge-{{ $proveedor->estado->status == 1 ? 'success' : 'danger' }}">
                                                        {{ $proveedor->estado->parametro->name ?? 'N/A' }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">SIN ESTADO</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Contratos/Convenios</th>
                                            <td class="py-3">
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-file-contract mr-1"></i>
                                                    {{ $proveedor->contratos_convenios_count ?? 0 }} contrato(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Productos</th>
                                            <td class="py-3">
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-boxes mr-1"></i>
                                                    {{ $proveedor->productos_count ?? 0 }} producto(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Creación</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($proveedor->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última Actualización</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($proveedor->updated_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        @if($proveedor->observaciones)
                                        <tr>
                                            <th class="py-3">Observaciones</th>
                                            <td class="py-3">{{ $proveedor->observaciones }}</td>
                                        </tr>
                                        @endif
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
                                @can('EDITAR PROVEEDOR')
                                    <a href="{{ route('inventario.proveedores.edit', $proveedor->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan

                                @can('ELIMINAR PROVEEDOR')
                                    <form action="{{ route('inventario.proveedores.destroy', $proveedor->id) }}" 
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
