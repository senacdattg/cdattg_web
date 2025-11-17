@extends('adminlte::page')

@section('title', 'Programas de Formación')

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        /* Asegurar que el footer no interfiera con el sidebar de AdminLTE */
        .footer {
            margin-left: 0 !important;
            padding-left: 0 !important;
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Asegurar que el contenido principal respete el sidebar */
        .content-wrapper {
            margin-left: 250px !important;
        }
        
        .main-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            overflow-y: auto !important;
            z-index: 1000 !important;
        }
        
        /* Responsive para móviles */
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0 !important;
            }
            
            .main-sidebar {
                transform: translateX(-100%) !important;
                transition: transform 0.3s ease !important;
            }
            
            .main-sidebar.sidebar-open {
                transform: translateX(0) !important;
            }
        }
        
        /* Estilos específicos para el footer */
        .footer {
            background-color: #f8f9fa !important;
            border-top: 1px solid #dee2e6 !important;
            margin-top: 2rem !important;
        }
        
        .footer-logo {
            max-height: 50px !important;
            width: auto !important;
        }
        
        .social-links a {
            color: #6c757d !important;
            font-size: 1.2rem !important;
            transition: color 0.3s ease !important;
        }
        
        .social-links a:hover {
            color: #007bff !important;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-graduation-cap" 
        title="Programas de Formación"
        subtitle="Gestión de programas de formación del SENA"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Programas de Formación', 'active' => true, 'icon' => 'fa-graduation-cap']
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <x-session-alerts />
            
            <div class="row">
                <div class="col-12">
                    @can('programa.create')
                        <x-table-filters 
                            action="{{ route('programa.store') }}"
                            method="POST"
                            title="Crear Programa de Formación"
                            icon="fa-plus-circle"
                        >
                            @include('programas.create')
                        </x-table-filters>
                    @endcan

                    <x-data-table 
                        title="Lista de Programas de Formación"
                        searchable="true"
                        searchAction="{{ route('programa.index') }}"
                        searchPlaceholder="Buscar por código, nombre..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Código', 'width' => '10%'],
                            ['label' => 'Nombre', 'width' => '30%'],
                            ['label' => 'Red de Conocimiento', 'width' => '20%'],
                            ['label' => 'Nivel', 'width' => '12%'],
                            ['label' => 'Horas totales', 'width' => '8%'],
                            ['label' => 'Horas etapa lectiva', 'width' => '8%'],
                            ['label' => 'Horas etapa productiva', 'width' => '8%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                        :pagination="$programas->links()"
                    >
                        <x-slot name="actions">
                            <div class="mr-3">
                                <select id="filterRedConocimiento" class="form-control form-control-sm" style="width: 150px;">
                                    <option value="">Todas las redes</option>
                                    @foreach(\App\Models\RedConocimiento::all() as $red)
                                        <option value="{{ $red->id }}">{{ $red->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mr-3">
                                <select id="filterNivelFormacion" class="form-control form-control-sm" style="width: 120px;">
                                    <option value="">Todos los niveles</option>
                                    @foreach(\App\Models\Parametro::whereHas('temas', function($query) { $query->where('temas.id', 6); })->get() as $nivel)
                                        <option value="{{ $nivel->id }}">{{ $nivel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mr-3">
                                <select id="filterStatus" class="form-control form-control-sm" style="width: 100px;">
                                    <option value="">Todos</option>
                                    <option value="1">Activos</option>
                                    <option value="0">Inactivos</option>
                                </select>
                            </div>
                        </x-slot>
                                        @forelse ($programas as $programa)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4">
                                                    <span class="badge badge-secondary">{{ $programa->codigo }}</span>
                                                </td>
                                                <td class="px-4 font-weight-medium">{{ $programa->nombre }}</td>
                                                <td class="px-4">
                                                    @if ($programa->redConocimiento)
                                                        <span class="text-primary">
                                                            <i class="fas fa-network-wired mr-1"></i>
                                                            {{ Str::limit($programa->redConocimiento->nombre, 30) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    @if ($programa->nivelFormacion)
                                                        <span class="text-success">
                                                            <i class="fas fa-layer-group mr-1"></i>
                                                            {{ $programa->nivelFormacion->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    <span class="badge badge-info">
                                                        {{ number_format($programa->horas_totales ?? 0) }} h
                                                    </span>
                                                </td>
                                                <td class="px-4">
                                                    <span class="badge badge-light text-primary">
                                                        {{ number_format($programa->horas_etapa_lectiva ?? 0) }} h
                                                    </span>
                                                </td>
                                                <td class="px-4">
                                                    <span class="badge badge-light text-success">
                                                        {{ number_format($programa->horas_etapa_productiva ?? 0) }} h
                                                    </span>
                                                </td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $programa->status ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $programa->status ? 'Activo' : 'Inactivo' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('programa.edit')
                                                            <form action="{{ route('programa.cambiarEstado', ['programa' => $programa->id]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-light btn-sm" data-toggle="tooltip" title="Cambiar estado">
                                                                    <i class="fas fa-sync text-success"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                        @can('programa.show')
                                                            <a href="{{ route('programa.show', ['programa' => $programa->id]) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('programa.edit')
                                                            <a href="{{ route('programa.edit', ['programa' => $programa->id]) }}" class="btn btn-light btn-sm" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('programa.delete')
                                                            <form action="{{ route('programa.destroy', ['programa' => $programa->id]) }}" method="POST" class="d-inline formulario-eliminar">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light btn-sm" data-toggle="tooltip" title="Eliminar">
                                                                    <i class="fas fa-trash text-danger"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data" style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay programas de formación registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                    </x-data-table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('components.confirm-delete-modal')
@endsection

@section('footer')
    @include('layouts.footer')
    @include('layouts.alertas')
@endsection

@section('js')
    @vite(['resources/js/pages/programas-index.js'])
@endsection