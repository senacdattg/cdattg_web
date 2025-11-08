@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
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
                            class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-2">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users-cog me-2"></i> Lista de personas
                            </h5>
                            <span class="text-muted small">Gestiona tus filtros de manera clara y eficiente.</span>
                        </div>
                        <div class="card-body p-0">
                            <x-data-table title="Lista de Personas" :paginated="false" :searchable="false"
                                tableId="personas-table" tableClass="table table-center table-sm table-striped mb-0"
                                tableWrapperClass="table-responsive personas-table-responsive rounded-bottom"
                                :columns="[
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
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8">
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
                                <div class="col-md-4">
                                    <div class="alert alert-light border text-muted mb-0 small">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Combina este filtro con la búsqueda global de la tabla para segmentar rápidamente.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 col-xxl-10 mx-auto">
                    <div class="row g-3 align-items-stretch">
                        <div class="col-12 col-lg-7 col-xl-8 order-2 order-lg-1">
                            <div class="card card-outline card-success shadow-sm h-100">
                                <div
                                    class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
                                    <div>
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-bar me-2"></i> Resumen general
                                        </h5>
                                        <span class="text-muted small">Monitorea el estado de tu base en tiempo real.</span>
                                    </div>
                                    <span class="badge bg-success mt-2 mt-md-0 text-uppercase">Actualizado</span>
                                </div>
                                <div class="card-body pb-2">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-lg-4">
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
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="info-box bg-light shadow-none h-100" data-toggle="tooltip"
                                                title="Personas que coinciden con la búsqueda actual y los filtros aplicados.">
                                                <span class="info-box-icon bg-success text-white"><i
                                                        class="fas fa-filter"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-muted text-uppercase small">Resultados
                                                        actuales</span>
                                                    <span class="info-box-number h4 mb-0 text-success"
                                                        id="total-personas-filtradas">—</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="info-box bg-light shadow-none h-100" data-toggle="tooltip"
                                                title="Cantidad de personas registradas en Sena Sofia Plus frente al total general.">
                                                <span class="info-box-icon bg-info text-white"><i
                                                        class="fas fa-graduation-cap"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-muted text-uppercase small">Registrados
                                                        en SOFIA</span>
                                                    <span class="info-box-number h4 mb-0 text-info"
                                                        id="total-personas-sofia">—</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5 col-xl-4 order-1 order-lg-2">
                            <div class="card card-outline card-primary shadow-sm mb-3 mb-lg-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-plus me-2"></i> Acciones rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <x-create-card url="{{ route('personas.create') }}" title="Crear Persona"
                                                icon="fa-plus-circle" permission="CREAR PERSONA" />
                                        </div>
                                        <div class="col-12">
                                            <x-create-card url="{{ route('personas.import.create') }}"
                                                title="Importar Personas (XLSX/CSV)" icon="fa-file-import"
                                                permission="CREAR PERSONA" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-outline card-info shadow-sm mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-lightbulb me-2"></i> Recomendaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0 small text-muted ps-3">
                                        <li>Mantén los datos actualizados antes de nuevas importaciones.</li>
                                        <li>Utiliza el filtro de estado para validar campañas activas.</li>
                                        <li>Revisa duplicados recientes desde la opción de importación.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="callout callout-warning shadow-sm mb-0">
                                <h5 class="mb-2 text-warning">
                                    <i class="fas fa-exclamation-circle me-2"></i> Buenas prácticas
                                </h5>
                                <p class="mb-2 small">Recuerda garantizar unicidad en documento, correo y celular. Las
                                    alertas se
                                    reportan directamente en la importación.</p>
                                <p class="mb-0 small text-muted">Sigue el principio DRY reutilizando plantillas y evita
                                    duplicar
                                    registros manuales.</p>
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
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-generico.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();

            const $estadoFilter = $('#filtro-estado');
            const $totalGeneral = $('#total-personas');
            const $totalFiltrado = $('#total-personas-filtradas');
            const $totalSofia = $('#total-personas-sofia');

            const formatNumber = function(value) {
                const numericValue = Number(value);
                return Number.isFinite(numericValue) ? numericValue.toLocaleString('es-CO') : '0';
            };

            const personasTable = $('#personas-table').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                stateSave: true,
                ajax: function(data, callback) {
                    axios.get('{{ route('personas.datatable') }}', {
                            params: Object.assign({}, data, {
                                estado: $estadoFilter.val()
                            })
                        })
                        .then(function(response) {
                            const json = response.data;

                            const totalGeneral = json.total_general ?? json.recordsTotal ?? 0;
                            const totalFiltrado = json.total_filtrado ?? json.recordsFiltered ??
                                totalGeneral;

                            $totalGeneral.text(formatNumber(totalGeneral));
                            $totalFiltrado.text(formatNumber(totalFiltrado));
                            const totalSofiaRegistrados = Number(json.sofia_registrados_filtrados ??
                                json.sofia_registrados_total ?? 0);
                            $totalSofia.text(
                                `${formatNumber(totalSofiaRegistrados)} / ${formatNumber(totalFiltrado)}`
                            );

                            callback(json);
                        })
                        .catch(function(error) {
                            console.error('Error al cargar personas:', error);
                            $totalGeneral.text('0');
                            $totalFiltrado.text('0');
                            $totalSofia.text('0 / 0');
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: []
                            });
                        });
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json',
                    processing: '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Cargando personas...',
                    emptyTable: 'No se encontraron personas registradas.',
                    zeroRecords: 'No hay resultados para los filtros aplicados.'
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'index',
                        name: 'id',
                        searchable: false,
                        className: 'align-middle text-muted'
                    },
                    {
                        data: 'nombre',
                        name: 'primer_nombre',
                        className: 'align-middle'
                    },
                    {
                        data: 'numero_documento',
                        name: 'numero_documento',
                        className: 'align-middle'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        className: 'align-middle'
                    },
                    {
                        data: 'celular',
                        name: 'celular',
                        className: 'align-middle'
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        orderable: false,
                        searchable: false,
                        className: 'align-middle text-center',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'estado_sofia',
                        name: 'estado_sofia',
                        orderable: false,
                        searchable: false,
                        className: 'align-middle text-center',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false,
                        className: 'align-middle text-center',
                        render: function(data) {
                            return data;
                        }
                    },
                ],
                columnDefs: [{
                    targets: 0,
                    orderable: true
                }],
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                dom: '<"row align-items-center mb-2 px-3 pt-3"<"col-sm-12 col-md-6 mb-2 mb-md-0"l><"col-sm-12 col-md-6 text-md-right"f>>rt<"row align-items-center mt-2 px-3 pb-3"<"col-sm-12 col-md-5 mb-2 mb-md-0"i><"col-sm-12 col-md-7"p>>'
            });

            $estadoFilter.on('change', function() {
                personasTable.draw();
            });

            $('#btn-limpiar-filtros').on('click', function() {
                $estadoFilter.val('todos');
                personasTable.search('');
                personasTable.draw();
            });
        });
    </script>
@endsection
