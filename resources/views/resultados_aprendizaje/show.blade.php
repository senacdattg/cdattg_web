@extends('adminlte::page')

@section('css')
    @vite(['resources/css/guias_aprendizaje.css'])
    <style>
        .dashboard-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .link_right_header {
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .link_right_header:hover {
            color: #4299e1;
        }
        .breadcrumb-item {
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .breadcrumb-item i {
            font-size: 0.8rem;
            margin-right: 0.4rem;
        }
        .breadcrumb-item a {
            color: #4a5568;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #718096;
        }
        .detail-table th { width: 30%; font-weight: 600; background-color: #f8f9fc; border-right: 1px solid #e3e6f0; }
        .detail-table td { padding: 0.75rem 1rem; }
        .detail-table tr { border-bottom: 1px solid #e3e6f0; }
        .detail-table tr:last-child { border-bottom: none; }
        .section-card { background: #f8f9fc; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; }
        .section-card-title { font-weight: 600; color: #4e73df; margin-bottom: 0.75rem; }
        .list-item { padding: 0.5rem 0; border-bottom: 1px solid #e3e6f0; }
        .list-item:last-child { border-bottom: none; }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-graduation-cap" 
        title="Resultados de Aprendizaje"
        subtitle="Gestión de resultados de aprendizaje del SENA"
        :breadcrumb="[['label' => 'Resultados de Aprendizaje', 'url' => route('resultados-aprendizaje.index') , 'icon' => 'fa-graduation-cap'], ['label' => 'Detalles', 'icon' => 'fa-info-circle', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('resultados-aprendizaje.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle del Resultado de Aprendizaje
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Código</th>
                                            <td class="py-3">{{ $resultadoAprendizaje->codigo }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">{{ $resultadoAprendizaje->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Duración</th>
                                            <td class="py-3">
                                                @if($resultadoAprendizaje->duracion)
                                                    <span class="badge badge-info">{{ formatear_horas($resultadoAprendizaje->duracion) }} horas</span>
                                                @else
                                                    <span class="text-muted">No especificada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha Inicio</th>
                                            <td class="py-3">
                                                @if($resultadoAprendizaje->fecha_inicio)
                                                    <i class="far fa-calendar mr-1"></i>
                                                    {{ $resultadoAprendizaje->fecha_inicio->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No especificada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha Fin</th>
                                            <td class="py-3">
                                                @if($resultadoAprendizaje->fecha_fin)
                                                    <i class="far fa-calendar mr-1"></i>
                                                    {{ $resultadoAprendizaje->fecha_fin->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No especificada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $resultadoAprendizaje->status == 1 ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $resultadoAprendizaje->status == 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                                @if($resultadoAprendizaje->estaVigente())
                                                    <span class="badge badge-success ml-2">Vigente</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Competencias Asociadas</th>
                                            <td class="py-3">
                                                <span class="badge badge-primary badge-pill">
                                                    {{ $resultadoAprendizaje->competencias->count() }} competencia(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Guías de Aprendizaje</th>
                                            <td class="py-3">
                                                <span class="badge badge-warning badge-pill">
                                                    {{ $resultadoAprendizaje->guiasAprendizaje->count() }} guía(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que crea</th>
                                            <td class="py-3 user-info">
                                                @if ($resultadoAprendizaje->userCreate)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $resultadoAprendizaje->userCreate->persona->primer_nombre ?? '' }}
                                                    {{ $resultadoAprendizaje->userCreate->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de creación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $resultadoAprendizaje->created_at ? $resultadoAprendizaje->created_at->diffForHumans() : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que modifica</th>
                                            <td class="py-3 user-info">
                                                @if ($resultadoAprendizaje->userEdit)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $resultadoAprendizaje->userEdit->persona->primer_nombre ?? '' }}
                                                    {{ $resultadoAprendizaje->userEdit->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de modificación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $resultadoAprendizaje->updated_at ? $resultadoAprendizaje->updated_at->diffForHumans() : 'N/A' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if($resultadoAprendizaje->competencias->count() > 0)
                            <div class="card-body">
                                <div class="section-card">
                                    <div class="section-card-title text-primary">
                                        <i class="fas fa-award mr-1"></i> Competencias Asociadas
                                    </div>
                                    @foreach($resultadoAprendizaje->competencias as $competencia)
                                        <div class="list-item">
                                            <strong>{{ $competencia->codigo ?? 'N/A' }}</strong> - {{ $competencia->nombre }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($resultadoAprendizaje->guiasAprendizaje->count() > 0)
                            <div class="card-body">
                                <div class="section-card">
                                    <div class="section-card-title text-primary">
                                        <i class="fas fa-book-open mr-1"></i> Guías de Aprendizaje Asociadas
                                    </div>
                                    @foreach($resultadoAprendizaje->guiasAprendizaje as $guia)
                                        <div class="list-item">
                                            <strong>{{ $guia->codigo }}</strong> - {{ $guia->nombre }}
                                            <span class="badge badge-{{ $guia->status == 1 ? 'success' : 'secondary' }} ml-2">
                                                {{ $guia->status == 1 ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR RESULTADO APRENDIZAJE')
                                    <form action="{{ route('resultados-aprendizaje.cambiarEstado', $resultadoAprendizaje) }}"
                                        method="POST" class="d-inline mx-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-sync mr-1"></i> Cambiar Estado
                                        </button>
                                    </form>
                                    <a href="{{ route('resultados-aprendizaje.edit', $resultadoAprendizaje) }}"
                                        class="btn btn-outline-info btn-sm mx-1">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                @can('ELIMINAR RESULTADO APRENDIZAJE')
                                    <button type="button" class="btn btn-outline-danger btn-sm mx-1" 
                                        data-rap="{{ $resultadoAprendizaje->codigo }}" 
                                        data-url="{{ route('resultados-aprendizaje.destroy', $resultadoAprendizaje) }}"
                                        onclick="confirmarEliminacion(this.dataset.rap, this.dataset.url)">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </button>
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
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/detalle-generico.js'])
@endsection

