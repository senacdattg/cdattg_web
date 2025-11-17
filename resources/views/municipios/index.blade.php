@extends('adminlte::page')

@section('title', 'Municipios')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Municipios"
        subtitle="Gestión de municipios"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Municipios', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
<section class="content mt-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                    <x-table-filters 
                        action="{{ route('municipio.store') }}"
                        method="POST"
                        title="Crear Municipio"
                        icon="fa-plus-circle"
                    >
                        @include('municipios.create')
                    </x-table-filters>

                    <x-data-table 
                        title="Lista de Municipios"
                        searchable="true"
                        searchAction="{{ route('municipio.index') }}"
                        searchPlaceholder="Buscar municipio..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Municipio', 'width' => '40%'], ['label' => 'Departamento', 'width' => '40%'], ['label' => 'Estado', 'width' => '20%'], ['label' => 'Acciones', 'width' => '35%', 'class' => 'text-center']]"
                        :pagination="$listademunicipios->links()"
                    >
GET
                    </x-data-table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('plugins.Chartjs', true)

@section('js')
    @vite(['resources/js/municipios.js'])
@endsection

