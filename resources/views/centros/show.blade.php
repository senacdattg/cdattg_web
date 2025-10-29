@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Centros de Formación"
        subtitle="Gestión de centros de formación"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Centros de Formación', 'url' => route('centros.index'), 'icon' => 'fa-cog'], ['label' => 'Detalles', 'icon' => 'fa-info-circle', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('centros.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle del Centro de Formación
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Centro de Formación</th>
                                            <td class="py-3">{{ $centro->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Regional</th>
                                            <td class="py-3">{{ $centro->regional->nombre ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Teléfono</th>
                                            <td class="py-3">
                                                @if($centro->telefono)
                                                    <i class="fas fa-phone mr-1"></i>
                                                    {{ $centro->telefono }}
                                                @else
                                                    <span class="text-muted">No disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Dirección</th>
                                            <td class="py-3">
                                                @if($centro->direccion)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $centro->direccion }}
                                                @else
                                                    <span class="text-muted">No disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Sitio Web</th>
                                            <td class="py-3">
                                                @if($centro->web)
                                                    <i class="fas fa-globe mr-1"></i>
                                                    <a href="{{ $centro->web }}" target="_blank">{{ $centro->web }}</a>
                                                @else
                                                    <span class="text-muted">No disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $centro->status === 1 ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $centro->status === 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que crea</th>
                                            <td class="py-3 user-info">
                                                @if ($centro->userCreated)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $centro->userCreated->persona->primer_nombre }}
                                                    {{ $centro->userCreated->persona->primer_apellido }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de creación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $centro->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que modifica</th>
                                            <td class="py-3 user-info">
                                                @if ($centro->userEdited)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $centro->userEdited->persona->primer_nombre }}
                                                    {{ $centro->userEdited->persona->primer_apellido }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de modificación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $centro->updated_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR CENTRO DE FORMACION')
                                    <form action="{{ route('centro.cambiarEstado', $centro->id) }}"
                                        method="POST" class="d-inline mx-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-sync mr-1"></i> Cambiar Estado
                                        </button>
                                    </form>
                                    <a href="{{ route('centros.edit', $centro->id) }}"
                                        class="btn btn-outline-info btn-sm mx-1">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                @can('ELIMINAR CENTRO DE FORMACION')
                                    <form action="{{ route('centros.destroy', $centro->id) }}"
                                        method="POST" class="d-inline mx-1"
                                        onsubmit="return confirm('¿Está seguro de eliminar este centro de formación?')">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
@endsection

