@extends('adminlte::page')

@section('css')
    @vite(['resources/css/temas.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-home" 
        title="Temas"
        subtitle="Gestión de temas del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => '{{ route('verificarLogin') }}', 'icon' => 'fa-home'], ['label' => 'Temas', 'icon' => 'fa-fw fa-paint-brush', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-12">
                    @can('CREAR TEMA')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear Tema
                                </h5>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                                    data-target="#createParameterForm" aria-expanded="true">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>

                            <div class="collapse show" id="createParameterForm">
                                <div class="card-body">
                                    @include('temas.create')
                                </div>
                            </div>
                        </div>
                    @endcan
                    <x-data-table 
                        title="Lista de Temas"
                        searchable="true"
                        searchAction="{{ route('tema.index') }}"
                        searchPlaceholder="Buscar tema..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Nombre', 'width' => '25%'], ['label' => 'Estado', 'width' => '15%', 'class' => 'text-center'], ['label' => 'Parámetros', 'width' => '40%', 'class' => 'text-center'], ['label' => 'Acciones', 'width' => '25%', 'class' => 'text-center']]"
                        :pagination="$listadetemas->links()"
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
