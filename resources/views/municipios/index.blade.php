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
        :breadcrumb="[['label' => 'Inicio', 'url' => '{{ route('verificarLogin') }}', 'icon' => 'fa-home'], ['label' => 'Municipios', 'icon' => 'fa-cog', 'active' => true]]"
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
                                <i class="fas fa-plus-circle mr-2"></i> Crear Municipio
                            </h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                                data-target="#createParameterForm" aria-expanded="true">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>

                        <div class="collapse show" id="createParameterForm">
                            <div class="card-body">
                                @include('municipios.create')
                            </div>
                        </div>
                    </div>

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
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/municipios.js'])
@endsection

