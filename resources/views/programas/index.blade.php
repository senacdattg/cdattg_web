@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
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
                            action="{{ route('programas.store') }}"
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
                        searchAction="{{ route('programas.index') }}"
                        searchPlaceholder="Buscar por código, nombre..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Código', 'width' => '10%'],
                            ['label' => 'Nombre', 'width' => '30%'],
                            ['label' => 'Red de Conocimiento', 'width' => '20%'],
                            ['label' => 'Nivel', 'width' => '15%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                        :pagination="$programas->links()"
                        :actionsSlot="'
                            <div class=\"mr-3\">
                                <select id=\"filterRedConocimiento\" class=\"form-control form-control-sm\" style=\"width: 150px;\">
                                    <option value=\"\">Todas las redes</option>
                                    @foreach(\\App\\Models\\RedConocimiento::all() as \$red)
                                        <option value=\"{{ \$red->id }}\">{{ \$red->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=\"mr-3\">
                                <select id=\"filterNivelFormacion\" class=\"form-control form-control-sm\" style=\"width: 120px;\">
                                    <option value=\"\">Todos los niveles</option>
                                    @foreach(\\App\\Models\\Parametro::whereHas(\'temas\', function(\$query) { \$query->where(\'temas.id\', 6); })->get() as \$nivel)
                                        <option value=\"{{ \$nivel->id }}\">{{ \$nivel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=\"mr-3\">
                                <select id=\"filterStatus\" class=\"form-control form-control-sm\" style=\"width: 100px;\">
                                    <option value=\"\">Todos</option>
                                    <option value=\"1\">Activos</option>
                                    <option value=\"0\">Inactivos</option>
                                </select>
                            </div>
                        '"
                    >
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

@section('js')
    @vite(['resources/js/pages/programas-index.js'])
@endsection