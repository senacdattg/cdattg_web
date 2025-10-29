@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Personas"
        subtitle="Gestión de personas del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Personas', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card 
                        url="{{ route('personas.create') }}"
                        title="Crear Persona"
                        icon="fa-plus-circle"
                        permission="CREAR PERSONA"
                    />

                    <x-data-table 
                        title="Lista de Personas"
                        searchable="true"
                        searchAction="{{ route('personas.index') }}"
                        searchPlaceholder="Buscar persona..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre y Apellido', 'width' => '25%'],
                            ['label' => 'Número de Documento', 'width' => '20%'],
                            ['label' => 'Correo Electrónico', 'width' => '25%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Opciones', 'width' => '15%', 'class' => 'text-center']
                        ]"
                        :pagination="$personas->links()"
                    >
                        @forelse ($personas as $persona)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $persona->nombre_completo }}</td>
                                <td>{{ $persona->numero_documento }}</td>
                                <td>{{ $persona->email }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $persona->status === 1 ? 'success' : 'danger' }}">
                                        {{ $persona->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('CAMBIAR ESTADO PERSONA')
                                            <form class="d-inline"
                                                action="{{ route('persona.cambiarEstadoPersona', $persona->id) }}"
                                                method="POST" title="Cambiar Estado"
                                                style="display: inline-block; margin-right: 2px;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-light">
                                                    <i class="fas fa-sync text-success"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        @can('VER PERSONA')
                                            <a href="{{ route('personas.show', $persona->id) }}"
                                                class="btn btn-sm btn-light" title="Ver"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR PERSONA')
                                            <a href="{{ route('personas.edit', $persona->id) }}"
                                                class="btn btn-sm btn-light" title="Editar"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR PERSONA')
                                            <form class="d-inline eliminar-persona-form"
                                                action="{{ route('personas.destroy', $persona->id) }}" method="POST"
                                                title="Eliminar"
                                                style="display: inline-block;"
                                                onsubmit="return confirm('¿Está seguro de eliminar esta persona?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay personas registradas</td>
                            </tr>
                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>

    @include('components.confirm-delete-modal')
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection
