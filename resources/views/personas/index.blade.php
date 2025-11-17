@extends('adminlte::page')

{{-- Activar plugins de AdminLTE --}}
@section('plugins.Datatables', true)
{{-- SweetAlert2 activado globalmente en config/adminlte.php --}}

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        @media (min-width: 992px) {
            .personas-table-responsive {
                overflow-x: visible;
            }
        }

        .info-box {
            min-height: 120px;
        }

        .info-box .info-box-number {
            font-weight: 700;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header icon="fa-cogs" title="Personas" subtitle="Gestión de personas del sistema" :breadcrumb="[
        ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
        ['label' => 'Personas', 'icon' => 'fa-cog', 'active' => true],
    ]" />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-xxl-10 mx-auto">
                    <div class="card card-outline card-secondary shadow-sm mb-3">
                        <div
                            class="card-header d-flex flex-column flex-md-row align-items-md-center
                                justify-content-md-between gap-2">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-users-cog me-2"></i> Lista de personas
                                </h5>
                                <span class="text-muted small">Gestiona tus filtros de manera clara y eficiente.</span>
                            </div>
                            <div class="d-flex flex-wrap mt-2 mt-md-0">
                                @can('CREAR PERSONA')
                                    <a href="{{ route('personas.create') }}" class="btn btn-primary mr-2 mb-2"
                                        @if (config('adminlte.livewire')) wire:navigate @endif>
                                        <i class="fas fa-plus-circle mr-1"></i> Crear Persona
                                    </a>
                                    <a href="{{ route('personas.import.create') }}" class="btn btn-success mb-2"
                                        @if (config('adminlte.livewire')) wire:navigate @endif>
                                        <i class="fas fa-file-import mr-1"></i> Importar
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <x-data-table title="Lista de Personas" :paginated="false" :searchable="false"
                                tableId="personas-table" tableClass="table table-center table-sm table-striped mb-0"
                                tableWrapperClass="table-responsive personas-table-responsive rounded-bottom"
                                data-datatable-url="{{ route('personas.datatable') }}" :columns="[
                                    ['label' => '#', 'width' => '5%'],
                                    ['label' => 'Nombres completos', 'width' => '25%'],
                                    ['label' => 'Documento', 'width' => '13%'],
                                    ['label' => 'Correo', 'width' => '22%'],
                                    ['label' => 'Celular', 'width' => '14%'],
                                    ['label' => 'Estado', 'width' => '7%'],
                                    ['label' => 'Estado Sofía', 'width' => '10%'],
                                    ['label' => 'Opciones', 'width' => '15%', 'class' => 'text-center'],
                                ]">
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <span class="spinner-border spinner-border-sm mr-2" role="status"
                                            aria-hidden="true"></span>
                                        Cargando registros...
                                    </td>
                                </tr>
                            </x-data-table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-xxl-10 mx-auto">
                    <div class="card card-outline card-warning shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-filter me-2"></i> Filtros inteligentes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label class="form-label text-muted text-uppercase small mb-1"
                                        for="filtro-estado">Filtrar por
                                        estado</label>
                                    <div class="input-group input-group-sm">
                                        <select id="filtro-estado" class="form-control">
                                            <option value="todos" selected>Todos</option>
                                            <option value="activos">Activos</option>
                                            <option value="inactivos">Inactivos</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="btn-limpiar-filtros" title="Restablecer filtros">
                                                <i class="fas fa-undo mr-1"></i> Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="alert alert-light border text-muted mb-0 small">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Combina este filtro con la búsqueda global de la tabla para
                                        segmentar rápidamente.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-xxl-10 mx-auto">
                    <div class="row align-items-stretch">
                        <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                            <div class="card card-outline card-success shadow-sm h-100">
                                <div
                                    class="card-header d-flex flex-column flex-md-row align-items-md-center
                                        justify-content-md-between">
                                    <div>
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-bar me-2"></i> Resumen general
                                        </h5>
                                        <span class="text-muted small">
                                            Monitorea el estado de tu base en tiempo real.
                                        </span>
                                    </div>
                                    <span class="badge bg-success mt-2 mt-md-0 text-uppercase">Actualizado</span>
                                </div>
                                <div class="card-body pb-2">
                                    <div class="row">
                                        <div class="col-sm-6 col-lg-4 mb-3">
                                            <div class="info-box bg-light shadow-none h-100" data-toggle="tooltip"
                                                title="Total de personas registradas en la base de datos.">
                                                <span class="info-box-icon bg-primary text-white"><i
                                                        class="fas fa-users"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-muted text-uppercase small">Total
                                                        registradas</span>
                                                    <span class="info-box-number h4 mb-0" id="total-personas">—</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4 mb-3">
                                            <div class="info-box bg-light shadow-none h-100" data-toggle="tooltip"
                                                title="Personas que coinciden con la búsqueda actual y
                                                    los filtros aplicados.">
                                                <span class="info-box-icon bg-success text-white"><i
                                                        class="fas fa-filter"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-muted text-uppercase small">
                                                        Resultados actuales
                                                    </span>
                                                    <span class="info-box-number h4 mb-0 text-success"
                                                        id="total-personas-filtradas">—</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4 mb-3">
                                            <div class="info-box bg-light shadow-none h-100" data-toggle="tooltip"
                                                title="Cantidad de personas registradas en Sena Sofia Plus
                                                    frente al total general.">
                                                <span class="info-box-icon bg-info text-white"><i
                                                        class="fas fa-graduation-cap"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-muted text-uppercase small">
                                                        Registrados en SOFIA
                                                    </span>
                                                    <span class="info-box-number h4 mb-0 text-info"
                                                        id="total-personas-sofia">—</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="card card-outline card-info shadow-sm">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-lightbulb me-2"></i> Recomendaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-info-circle mr-1"></i> Generales
                                    </h6>
                                    <ul class="mb-3 small text-muted ps-3">
                                        <li>
                                            Mantén los datos actualizados antes de nuevas importaciones.
                                        </li>
                                        <li>
                                            Utiliza el filtro de estado para validar campañas activas.
                                        </li>
                                        <li>
                                            Revisa duplicados recientes desde la opción de importación.
                                        </li>
                                    </ul>

                                    <div class="alert alert-warning mb-0">
                                        <h6 class="alert-heading mb-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Importante
                                        </h6>
                                        <p class="mb-2 small">
                                            Asegúrese de que cada persona tenga un número de documento, correo y
                                            celular único. Los registros duplicados serán reportados durante la
                                            importación.
                                        </p>
                                        <p class="mb-0 small">
                                            Utilice la plantilla proporcionada para facilitar el proceso y evite crear
                                            registros duplicados manualmente.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('components.confirm-delete-modal')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@push('js')
    @vite([
        'resources/js/app.js',
        'resources/js/parametros.js',
        'resources/js/pages/formularios-generico.js',
        'resources/js/pages/personas.js',
    ])
@endpush
