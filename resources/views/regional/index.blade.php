@extends('adminlte::page')

@section('title', "Regionales")

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Regionales"
        subtitle="Gestión de regionales"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Regionales', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
<section class="content mt-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <x-table-filters 
                    action="{{ route('regional.store') }}"
                    method="POST"
                    title="Crear Regional"
                    icon="fa-plus-circle"
                >
                    @include('regional.create')
                </x-table-filters>

                <x-data-table 
                        title="Lista de Regionales"
                        searchable="true"
                        searchAction="{{ route('regional.index') }}"
                        searchPlaceholder="Buscar regional..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Regional', 'width' => '40%'], ['label' => 'Departamento', 'width' => '40%'], ['label' => 'Estado', 'width' => '20%'], ['label' => 'Acciones', 'width' => '35%', 'class' => 'text-center']]"
                        :pagination="$listaderegionales->links()"
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
@endsection

@section('js')
    @vite(['resources/js/regional.js'])
@endsection