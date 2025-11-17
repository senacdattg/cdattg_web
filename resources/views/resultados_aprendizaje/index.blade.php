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
        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
        }
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-graduation-cap" 
        title="Resultados de Aprendizaje"
        subtitle="Gestión de resultados de aprendizaje del SENA"
        :breadcrumb="[['label' => 'Inicio', 'url' => url('/') , 'icon' => 'fa-home'], ['label' => 'Resultados de Aprendizaje', 'icon' => 'fa-graduation-cap', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <x-session-alerts />

            <div class="row">
                <div class="col-12">
                    <x-create-card 
                        url="{{ route('resultados-aprendizaje.create') }}"
                        title="Crear Resultado de Aprendizaje"
                        icon="fa-plus-circle"
                        permission="CREAR RESULTADO APRENDIZAJE"
                    />

                    <x-data-table 
                        title="Lista de Resultados de Aprendizaje"
                        searchable="true"
                        searchAction="{{ route('resultados-aprendizaje.index') }}"
                        searchPlaceholder="Buscar por código, nombre..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Código', 'width' => '15%'],
                            ['label' => 'Nombre', 'width' => '35%'],
                            ['label' => 'Duración', 'width' => '10%'],
                            ['label' => 'Estado', 'width' => '15%'],
                            ['label' => 'Guías', 'width' => '10%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                        :pagination="$resultadosAprendizaje->links()"
                    >
                        <x-slot name="actions">
                            <div class="mr-2">
                                <select id="filterCompetencia" class="form-control form-control-sm" style="width: 200px;">
                                    <option value="">Todas las competencias</option>
                                    @php
                                        $competencias = \App\Models\Competencia::orderBy('nombre')->get();
                                    @endphp
                                    @foreach($competencias as $competencia)
                                        <option value="{{ $competencia->id }}">{{ Str::limit($competencia->nombre, 25) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mr-2">
                                <select id="filterStatus" class="form-control form-control-sm" style="width: 100px;">
                                    <option value="">Todos</option>
                                    <option value="1">Activos</option>
                                    <option value="0">Inactivos</option>
                                </select>
                            </div>
                        </x-slot>
                                        @forelse ($resultadosAprendizaje as $resultado)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4 font-weight-medium">{{ $resultado->codigo }}</td>
                                                <td class="px-4">{{ $resultado->nombre }}</td>
                                                <td class="px-4">
                                                    @if($resultado->duracion)
                                                        <span class="badge badge-info">{{ formatear_horas($resultado->duracion) }}h</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $resultado->status == 1 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $resultado->status == 1 ? 'Activo' : 'Inactivo' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <span class="badge badge-primary">{{ $resultado->guiasAprendizaje->count() }}</span>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('VER RESULTADO APRENDIZAJE')
                                                            <a href="{{ route('resultados-aprendizaje.show', $resultado) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR RESULTADO APRENDIZAJE')
                                                            <a href="{{ route('resultados-aprendizaje.edit', $resultado) }}" 
                                                                class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('ELIMINAR RESULTADO APRENDIZAJE')
                                                            <button type="button" class="btn btn-light btn-sm" 
                                                                data-rap="{{ $resultado->codigo }}" 
                                                                data-url="{{ route('resultados-aprendizaje.destroy', $resultado) }}"
                                                                onclick="confirmarEliminacion(this.dataset.rap, this.dataset.url)"
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
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data" 
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay resultados de aprendizaje registrados</p>
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
    @vite(['resources/js/pages/resultados-aprendizaje-index.js'])
@endsection

