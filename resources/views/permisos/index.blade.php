@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Permisos"
        subtitle="Gestión de permisos del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Permisos', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
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
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->persona->nombre_completo }}</td>
                                    <td>{{ $user->persona->numero_documento }}</td>
                                    <td>{{ $user->persona->email }}</td>
                                    <td>
                                        @php
                                            $roles = $user->getRoleNames();
                                            $primaryRole = $roles->first() ?? 'Sin rol';
                                        @endphp
                                        <span class="badge badge-primary">{{ $primaryRole }}</span>
                                        @if($roles->count() > 1)
                                            <small class="text-muted d-block">(+{{ $roles->count() - 1 }} más)</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $user->persona->user->status === 1 ? 'success' : 'danger' }}">
                                            {{ $user->persona->user->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a class="btn btn-sm btn-light"
                                                href="{{ route('permiso.show', ['user' => $user->id]) }}"
                                                title="Ver Permisos"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        </div>
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
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
    @vite(['resources/js/parametros.js'])
@endsection
