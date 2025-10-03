@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-graduation-cap text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Programa de Formación</h1>
                        <p class="text-muted mb-0 font-weight-light">Detalles del programa de formación</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('programa.index') }}" class="link_right_header">
                                    <i class="fas fa-graduation-cap"></i> Programas de Formación
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-info-circle"></i> Detalles del programa
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
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
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Confirmar eliminación
        $('.formulario-eliminar').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const nombre = '{{ $programa->nombre }}';
            
            if (confirm(`¿Está seguro de que desea eliminar el programa "${nombre}"?`)) {
                form.off('submit').submit();
            }
        });
    });
</script>
@endsection