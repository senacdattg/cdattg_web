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
        :breadcrumb="[['label' => 'Inicio', 'url' => '{{ route('verificarLogin') }}', 'icon' => 'fa-home'], ['label' => 'Redes de Conocimiento', 'icon' => 'fa-network-wired', 'active' => true]]"
    />
@endsection

@section('content')
<section class="content mt-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4 no-hover">
                    <div class="card-header bg-white py-3 d-flex align-items-center">
                        <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                            <i class="fas fa-plus-circle mr-2"></i> Crear Red de Conocimiento
                        </h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                            data-target="#createRedConocimientoForm" aria-expanded="true">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <div class="collapse show" id="createRedConocimientoForm">
                        <div class="card-body">
                            @include('red_conocimiento.create')
                        </div>
                    </div>
                </div>

                <x-data-table 
                        title="Lista de Redes de Conocimiento"
                        searchable="true"
                        searchAction="{{ route('red-conocimiento.index') }}"
                        searchPlaceholder="Buscar red de conocimiento..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Red de Conocimiento', 'width' => '40%'], ['label' => 'Regional', 'width' => '25%'], ['label' => 'Estado', 'width' => '15%'], ['label' => 'Acciones', 'width' => '35%', 'class' => 'text-center']]"
                        :pagination="$listaderedesdeconocimiento->links()"
                    >
GET
                    </x-data-table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('footer')
    @include('layout.footer')
    @include('layout.alertas')
@endsection

@section('js')
    @vite(['resources/js/red-conocimiento.js'])
@endsection
