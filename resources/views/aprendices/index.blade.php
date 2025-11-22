@extends('adminlte::page')

@section('title', 'Aprendices')

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            background-color: #fff !important;
        }
        
        .select2-container--bootstrap4 .select2-selection--single:focus {
            border-color: #80bdff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            padding: 0 !important;
            line-height: 1.5 !important;
            color: #495057 !important;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            color: #6c757d !important;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px) !important;
        }
        
        .form-control.select2 {
            height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
        }
        
        .select2-container--bootstrap4.select2-container--focus .select2-selection--single {
            border-color: #80bdff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-user-graduate" 
        title="Aprendices"
        subtitle="Gestión de aprendices"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Aprendices', 'active' => true, 'icon' => 'fa-user-graduate']
        ]"
    />
@endsection

@section('content')
<section class="content mt-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @php
                    $aprendicesIncompletos = $aprendices->filter(function($a) {
                        return is_null($a->persona);
                    });
                @endphp
                
                @if($aprendicesIncompletos->count() > 0)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¡Atención!</strong> 
                        Hay {{ $aprendicesIncompletos->count() }} aprendiz(es) con datos incompletos (sin persona asociada).
                        Están resaltados en rojo en la tabla. Por favor, edítalos para corregir los datos.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                
                @can('CREAR APRENDIZ')
                    <div class="accordion mb-4" id="accordionCrearAprendiz">
                        <div class="card shadow-sm no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center" id="headingCrearAprendiz">
                                <h2 class="mb-0 w-100">
                                    <button
                                        class="btn btn-link w-100 text-left d-flex align-items-center text-decoration-none font-weight-bold text-primary px-0"
                                        type="button" data-toggle="collapse" data-target="#collapseCrearAprendiz"
                                        aria-expanded="false" aria-controls="collapseCrearAprendiz">
                                        <i class="fas fa-plus-circle mr-2"></i> Crear Aprendiz
                                        <i class="fas fa-chevron-down ml-auto"></i>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseCrearAprendiz" class="collapse" aria-labelledby="headingCrearAprendiz"
                                data-parent="#accordionCrearAprendiz">
                                <div class="card-body">
                                    @include('aprendices.create')
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                
                <!-- Filtros -->
                <x-table-filters 
                    action="{{ route('aprendices.index') }}"
                    method="GET"
                    title="Filtros de Búsqueda"
                    icon="fa-filter"
                >
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="search" class="form-label">Buscar por nombre o documento</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                    placeholder="Ingrese nombre o número de documento" 
                                    value="{{ request('search') }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="ficha_id" class="form-label">Filtrar por ficha</label>
                                <select name="ficha_id" id="ficha_id" class="form-control">
                                    <option value="">Todas las fichas</option>
                                    @foreach($fichas as $ficha)
                                        <option value="{{ $ficha->id }}" 
                                            {{ request('ficha_id') == $ficha->id ? 'selected' : '' }}>
                                            {{ $ficha->ficha }} - {{ $ficha->programaFormacion->nombre ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </x-table-filters>

                <!-- Tabla de Aprendices -->
                <x-data-table 
                    title="Lista de Aprendices"
                    searchable="false"
                    :columns="[
                        ['label' => '#', 'width' => '3%'],
                        ['label' => 'Nombre y Apellido', 'width' => '18%'],
                        ['label' => 'Documento', 'width' => '10%'],
                        ['label' => 'Ficha Principal', 'width' => '10%'],
                        ['label' => 'Correo Electrónico', 'width' => '20%'],
                        ['label' => 'Estado', 'width' => '10%'],
                        ['label' => 'Acciones', 'width' => '29%', 'class' => 'text-center']
                    ]"
                    :pagination="$aprendices->links()"
                >
                    <x-slot name="actions">
                        <a href="{{ route('aprendices.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Crear Aprendiz
                        </a>
                    </x-slot>
                                    @forelse ($aprendices as $aprendiz)
                                        <tr class="{{ !$aprendiz->persona ? 'table-danger' : '' }}">
                                            <td class="px-3">
                                                {{ $loop->iteration + ($aprendices->currentPage() - 1) * $aprendices->perPage() }}
                                                @if(!$aprendiz->persona)
                                                    <i class="fas fa-exclamation-triangle text-danger ml-1" data-toggle="tooltip" title="Aprendiz sin persona asociada"></i>
                                                @endif
                                            </td>
                                            <td class="px-3 font-weight-medium">
                                                {{ $aprendiz->persona?->nombre_completo ?? 'Sin información' }}
                                                @if(!$aprendiz->persona)
                                                    <span class="badge badge-danger ml-2">¡Datos incompletos!</span>
                                                @endif
                                            </td>
                                            <td class="px-3">{{ $aprendiz->persona?->numero_documento ?? 'N/A' }}</td>
                                            <td class="px-3">
                                                {{-- Debug: ID en BD: {{ $aprendiz->ficha_caracterizacion_id ?? 'NULL' }} --}}
                                                @if($aprendiz->fichaCaracterizacion)
                                                    <span class="badge badge-info">
                                                        {{ $aprendiz->fichaCaracterizacion->ficha }}
                                                    </span>
                                                @elseif($aprendiz->ficha_caracterizacion_id)
                                                    <span class="badge badge-warning">
                                                        ID: {{ $aprendiz->ficha_caracterizacion_id }} (No cargada)
                                                    </span>
                                                @else
                                                    <span class="text-muted small">Sin asignar</span>
                                                @endif
                                            </td>
                                            <td class="px-3">{{ $aprendiz->persona?->email ?? 'N/A' }}</td>
                                            <td class="px-3">
                                                <span class="badge badge-{{ $aprendiz->estado ? 'success' : 'danger' }} px-3 py-2">
                                                    <i class="fas fa-circle mr-1" style="font-size: 6px; vertical-align: middle;"></i>
                                                    {{ $aprendiz->estado ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td class="px-3 text-center" style="white-space: nowrap;">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @can('EDITAR APRENDIZ')
                                                        <form action="{{ route('aprendices.cambiarEstado', $aprendiz->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-light" data-toggle="tooltip" title="Cambiar estado">
                                                                <i class="fas fa-sync text-success"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('VER APRENDIZ')
                                                        @if($aprendiz->persona)
                                                            <a href="{{ route('aprendices.show', $aprendiz->id) }}" class="btn btn-light" data-toggle="tooltip" title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn btn-light" disabled data-toggle="tooltip" title="No se puede ver - datos incompletos">
                                                                <i class="fas fa-eye text-muted"></i>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                    @can('EDITAR APRENDIZ')
                                                        @if(!$aprendiz->persona)
                                                            <a href="{{ route('aprendices.edit', $aprendiz->id) }}" class="btn btn-warning" data-toggle="tooltip" title="¡Corregir datos!">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('aprendices.edit', $aprendiz->id) }}" class="btn btn-light" data-toggle="tooltip" title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    @can('ELIMINAR APRENDIZ')
                                                        <form action="{{ route('aprendices.destroy', $aprendiz->id) }}" method="POST" class="d-inline formulario-eliminar">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-light" data-toggle="tooltip" title="Eliminar">
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
                                                <p class="text-muted mt-3">No se encontraron aprendices</p>
                                            </td>
                                        </tr>
                                    @endforelse
                </x-data-table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('footer')
    @include('layouts.footer')
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
@endsection

@section('plugins.Select2', true)

@section('js')
    @vite(['resources/js/aprendices.js'])
    <script>
        $(document).ready(function() {
            // Inicializar Select2 cuando se expanda el acordeón
            $('#collapseCrearAprendiz').on('shown.bs.collapse', function () {
                $('#persona_id, #ficha_caracterizacion_id').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: function() {
                        return $(this).data('placeholder') || 'Seleccione una opción';
                    }
                });
            });
        });
    </script>
@endsection

