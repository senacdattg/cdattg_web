@extends('adminlte::page')

@section('title', "Redes de Conocimiento")

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-network-wired" 
        title="Redes de Conocimiento"
        subtitle="Gestión de redes de conocimiento"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Redes de Conocimiento', 'icon' => 'fa-network-wired', 'active' => true]]"
    />
@endsection

@section('content')
<section class="content mt-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @can('CREAR RED CONOCIMIENTO')
                    <div class="accordion mb-4" id="accordionCrearRedConocimiento">
                        <div class="card shadow-sm no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center" id="headingCrearRedConocimiento">
                                <h2 class="mb-0 w-100">
                                    <button
                                        class="btn btn-link w-100 text-left d-flex align-items-center text-decoration-none font-weight-bold text-primary px-0"
                                        type="button" data-toggle="collapse" data-target="#collapseCrearRedConocimiento"
                                        aria-expanded="false" aria-controls="collapseCrearRedConocimiento">
                                        <i class="fas fa-plus-circle mr-2"></i> Crear Red de Conocimiento
                                        <i class="fas fa-chevron-down ml-auto"></i>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseCrearRedConocimiento" class="collapse" aria-labelledby="headingCrearRedConocimiento"
                                data-parent="#accordionCrearRedConocimiento">
                                <div class="card-body">
                                    @include('red_conocimiento.create')
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                <x-data-table 
                        title="Lista de Redes de Conocimiento"
                        searchable="true"
                        searchAction="{{ route('red-conocimiento.index') }}"
                        searchPlaceholder="Buscar red de conocimiento..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Red de Conocimiento', 'width' => '40%'], ['label' => 'Regional', 'width' => '25%'], ['label' => 'Estado', 'width' => '15%'], ['label' => 'Acciones', 'width' => '35%', 'class' => 'text-center']]"
                        :pagination="$redesConocimiento->links()"
                    >
                        @forelse($redesConocimiento as $index => $red)
                            <tr>
                                <td>{{ $redesConocimiento->firstItem() + $index }}</td>
                                <td>{{ $red->nombre }}</td>
                                <td>{{ $red->regional->nombre ?? 'N/A' }}</td>
                                <td>
                                    @if($red->status)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <x-action-buttons 
                                        :show="true"
                                        :edit="true"
                                        :delete="true"
                                        showUrl="{{ route('red-conocimiento.show', $red->id) }}"
                                        editUrl="{{ route('red-conocimiento.edit', $red->id) }}"
                                        deleteUrl="{{ route('red-conocimiento.destroy', $red->id) }}"
                                        showTitle="Ver detalles de {{ $red->nombre }}"
                                        editTitle="Editar {{ $red->nombre }}"
                                        deleteTitle="Eliminar {{ $red->nombre }}"
                                        showPermission="VER RED CONOCIMIENTO"
                                        editPermission="EDITAR RED CONOCIMIENTO"
                                        deletePermission="ELIMINAR RED CONOCIMIENTO"
                                        
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay redes de conocimiento registradas</td>
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

@section('js')
    @vite(['resources/js/red-conocimiento.js'])
@endsection
