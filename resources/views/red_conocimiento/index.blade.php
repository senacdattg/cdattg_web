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
                <x-table-filters 
                    action="{{ route('red-conocimiento.store') }}"
                    method="POST"
                    title="Crear Red de Conocimiento"
                    icon="fa-plus-circle"
                >
                    @include('red_conocimiento.create')
                </x-table-filters>

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
