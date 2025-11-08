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
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-6">
                            <x-create-card url="{{ route('personas.create') }}" title="Crear Persona" icon="fa-plus-circle"
                                permission="CREAR PERSONA" />
                        </div>
                        <div class="col-md-6">
                            <x-create-card url="{{ route('personas.import.create') }}" title="Importar Personas (XLSX/CSV)"
                                icon="fa-file-import" permission="CREAR PERSONA" />
                        </div>
                    </div>

                    <div class="row align-items-stretch mb-3">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card shadow-sm h-100 border-0" data-toggle="tooltip"
                                title="Total de personas registradas en la base de datos.">
                                <div class="card-body py-3">
                                    <span class="text-muted text-uppercase small d-block mb-1">Total registradas</span>
                                    <h2 class="font-weight-bold text-primary mb-0 display-4">
                                        <span id="total-personas">—</span>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card shadow-sm h-100 border-0" data-toggle="tooltip"
                                title="Personas que coinciden con la búsqueda actual y los filtros aplicados.">
                                <div class="card-body py-3">
                                    <span class="text-muted text-uppercase small d-block mb-1">Resultados actuales</span>
                                    <h2 class="font-weight-bold text-success mb-0 display-4">
                                        <span id="total-personas-filtradas">—</span>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card shadow-sm h-100 border-0" data-toggle="tooltip"
                                title="Cantidad de personas registradas en Sena Sofia Plus frente al total general.">
                                <div class="card-body py-3">
                                    <span class="text-muted text-uppercase small d-block mb-1">Registrados en SOFIA</span>
                                    <h2 class="font-weight-bold text-info mb-0 display-4">
                                        <span id="total-personas-sofia">—</span>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-3 mb-3">
                            <div class="card shadow-sm h-100 border-0">
                                <div class="card-body py-3">
                                    <label class="text-muted text-uppercase small mb-2" for="filtro-estado">Filtrar por
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
                                    <small class="text-muted d-block mt-2">Combina este filtro con la búsqueda de DataTable
                                        para segmentar registros rápidamente.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-data-table title="Lista de Personas" :paginated="false" :searchable="false" tableId="personas-table"
                        tableClass="table table-center table-sm table-borderless table-striped mb-0"
                        tableWrapperClass="table-responsive rounded personas-table-responsive" :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombres completos', 'width' => '25%'],
                            ['label' => 'Documento', 'width' => '13%'],
                            ['label' => 'Correo', 'width' => '18%'],
                            ['label' => 'Teléfono', 'width' => '10%'],
                            ['label' => 'Celular', 'width' => '10%'],
                            ['label' => 'Estado', 'width' => '7%'],
                            ['label' => 'Estado Sofía', 'width' => '10%'],
                            ['label' => 'Opciones', 'width' => '15%', 'class' => 'text-center'],
                        ]">
                        <tr>
                            <td colspan="9" class="text-center text-muted">Cargando registros...</td>
                        </tr>
                    </x-data-table>
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
                        data: 'telefono',
                        name: 'telefono',
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
