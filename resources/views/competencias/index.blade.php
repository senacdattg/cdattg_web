@extends('adminlte::page')

@section('css')
    @vite(['resources/css/competencias.css'])
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

        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }
    </style>
@endsection

@section('content_header')
    <x-page-header icon="fa-clipboard-list" title="Competencias" subtitle="Gestión de competencias del SENA" :breadcrumb="[
            ['label' => 'Inicio', 'url' => url('/'), 'icon' => 'fa-home'],
            ['label' => 'Competencias', 'active' => true, 'icon' => 'fa-clipboard-list']
        ]" />
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
                    @can('CREAR COMPETENCIA')
                        <div class="accordion mb-4" id="accordionCrearCompetencia">
                            <div class="card shadow-sm no-hover">
                                <div class="card-header bg-white py-3 d-flex align-items-center" id="headingCrearCompetencia">
                                    <h2 class="mb-0 w-100">
                                        <button
                                            class="btn btn-link w-100 text-left d-flex align-items-center text-decoration-none font-weight-bold text-primary px-0"
                                            type="button" data-toggle="collapse" data-target="#collapseCrearCompetencia"
                                            aria-expanded="false" aria-controls="collapseCrearCompetencia">
                                            <i class="fas fa-plus-circle mr-2"></i> Crear Competencia
                                            <i class="fas fa-chevron-down ml-auto"></i>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseCrearCompetencia" class="collapse" aria-labelledby="headingCrearCompetencia"
                                    data-parent="#accordionCrearCompetencia">
                                    @include('competencias.create', ['inAccordion' => true])
                                </div>
                            </div>
                        </div>
                    @endcan

                    <x-data-table title="Lista de Competencias" searchable="true"
                        searchAction="{{ route('competencias.index') }}" searchPlaceholder="Buscar competencia..."
                        searchValue="{{ request('search') }}" :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Código norma', 'width' => '12%'],
                            ['label' => 'Nombre de la competencia', 'width' => '30%'],
                            ['label' => 'Norma / Unidad', 'width' => '20%'],
                            ['label' => 'Programas', 'width' => '15%'],
                            ['label' => 'Duración máx. (h)', 'width' => '8%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center']
                        ]" :pagination="$competencias->links()">
                        @forelse ($competencias as $competencia)
                            <tr>
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td class="px-4 font-weight-medium">{{ $competencia->codigo }}</td>
                                <td class="px-4">{{ Str::limit($competencia->nombre, 60) }}</td>
                                <td class="px-4">{{ Str::limit($competencia->descripcion, 70) }}</td>
                                <td class="px-4">
                                    @forelse($competencia->programasFormacion as $programa)
                                        <span class="badge badge-light text-primary border mr-1 mb-1 d-inline-block">
                                            {{ $programa->codigo }}
                                        </span>
                                    @empty
                                        <span class="text-muted">Sin programas</span>
                                    @endforelse
                                </td>
                                <td class="px-4">
                                    @if($competencia->duracion)
                                        <span class="badge badge-info">{{ number_format($competencia->duracion, 0) }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 text-center">
                                    <div class="mb-2">
                                        <span class="badge badge-{{ $competencia->status ? 'success' : 'secondary' }}">
                                            {{ $competencia->status ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        @can('VER COMPETENCIA')
                                            <a href="{{ route('competencias.show', $competencia) }}" class="btn btn-outline-info"
                                                data-toggle="tooltip" title="Ver Detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('GESTIONAR RESULTADOS COMPETENCIA')
                                            <a href="{{ route('competencias.gestionarResultados', $competencia) }}"
                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Gestionar Resultados">
                                                <i class="fas fa-tasks text-success"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR COMPETENCIA')
                                            <a href="{{ route('competencias.edit', $competencia) }}" class="btn btn-light btn-sm"
                                                data-toggle="tooltip" title="Editar">
                                                <i class="fas fa-pencil-alt text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR COMPETENCIA')
                                            <button type="button" class="btn btn-light btn-sm"
                                                data-competencia="{{ $competencia->codigo }}"
                                                data-url="{{ route('competencias.destroy', $competencia) }}"
                                                onclick="confirmarEliminacion(this.dataset.competencia, this.dataset.url)"
                                                data-toggle="tooltip" title="Eliminar">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data" style="width: 120px" class="mb-3">
                                    <p class="text-muted">No hay competencias registradas</p>
                                </td>
                            </tr>
                        @endforelse
                    </x-data-table>
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
    @vite(['resources/js/pages/competencias-index.js'])
@endsection
 
 