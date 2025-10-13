@extends('adminlte::page')

@section('content_header')
    <x-page-header 
        icon="fa-users" 
        title="Personas"
        subtitle="Gestión de personas del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Personas', 'active' => true, 'icon' => 'fa-users']]"
    />
@endsection

@section('content')

    <section class="content mt-4">
        <div class="container-fluid">
            <x-session-alerts />
            
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
                                        <td class="project-state">
                                            <span class="badge badge-{{ $persona->status === 1 ? 'success' : 'danger' }}">
                                                {{ $persona->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                            </span>
                                        </td>
                                        <td class="project-actions">
                                            @can('CAMBIAR ESTADO PERSONA')
                                                <form class="d-inline"
                                                    action="{{ route('persona.cambiarEstadoPersona', $persona->id) }}"
                                                    method="POST" title="Cambiar Estado">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('VER PERSONA')
                                                <a href="{{ route('personas.show', $persona->id) }}"
                                                    class="btn btn-warning btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('EDITAR PERSONA')
                                                <a href="{{ route('personas.edit', $persona->id) }}"
                                                    class="btn btn-info btn-sm" title="Editar">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            @endcan
                                            @can('ELIMINAR PERSONA')
                                                <form class="d-inline eliminar-persona-form"
                                                    action="{{ route('personas.destroy', $persona->id) }}" method="POST"
                                                    title="Eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No hay personas registradas</td>
                                    </tr>
                                @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
    </div>

    @include('components.confirm-delete-modal')
@endsection

@section('js')
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection
