@extends('adminlte::page')

@section('content_header')
    <x-page-header 
        icon="fa-shield-alt" 
        title="Permisos"
        subtitle="Gestión de permisos del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'], ['label' => 'Permisos', 'active' => true, 'icon' => 'fa-shield-alt']]"
    />
@endsection

@section('content')

    <section class="content mt-4">
        <div class="container-fluid">
            <x-session-alerts />
            
            <div class="row">
                <div class="col-12">
                    <x-data-table 
                        title="Lista de Permisos"
                        searchable="true"
                        searchAction="{{ route('permiso.index') }}"
                        searchPlaceholder="Buscar por nombre o documento..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre y Apellido', 'width' => '20%'],
                            ['label' => 'Número de Documento', 'width' => '15%'],
                            ['label' => 'Correo Electrónico', 'width' => '20%'],
                            ['label' => 'Roles Asignados', 'width' => '20%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                        :pagination="$users->links()"
                    >
                                    @forelse ($users as $user)
                                        @if ($user->id != Auth::user()->id)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $user->persona->nombre_completo }}</td>
                                                <td>{{ $user->persona->numero_documento }}</td>
                                                <td>{{ $user->persona->email }}</td>
                                                <td>
                                                    {{ $user->getRoleNames()->implode(', ') }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $user->persona->user->status === 1 ? 'success' : 'danger' }}">
                                                        {{ $user->persona->user->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a class="btn btn-warning btn-sm"
                                                        href="{{ route('permiso.show', ['user' => $user->id]) }}"
                                                        title="Ver Permisos">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No hay usuarios registrados</td>
                                        </tr>
                                    @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
    </div>
@endsection
