@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Parámetros"
        subtitle="Gestión de parámetros del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Parámetros', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @can('CREAR PARAMETRO')
                        <x-table-filters 
                            action="{{ route('parametro.store') }}"
                            method="POST"
                            title="Crear Parámetro"
                            icon="fa-plus-circle"
                        >
                            @include('parametros.create')
                        </x-table-filters>
                    @endcan

                    <x-data-table 
                        title="Lista de Parámetros"
                        searchable="true"
                        searchAction="{{ route('parametro.index') }}"
                        searchPlaceholder="Buscar parámetro..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Nombre', 'width' => '40%'], ['label' => 'Estado', 'width' => '20%'], ['label' => 'Acciones', 'width' => '35%', 'class' => 'text-center']]"
                        :pagination="$listadeparámetros->links()"
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
@endsection
