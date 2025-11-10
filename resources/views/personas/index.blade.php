@extends('adminlte::page')

{{-- Activar plugins de AdminLTE --}}
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

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
                                    <a href="{{ route('personas.create') }}" class="btn btn-primary mr-2 mb-2">
                                        <i class="fas fa-plus-circle mr-1"></i> Crear Persona
                                    </a>
                                    <a href="{{ route('personas.import.create') }}" class="btn btn-success mb-2">
                                        <i class="fas fa-file-import mr-1"></i> Importar
                                    </a>
                                @endcan
                            </div>
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
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/bootstrap.js'])
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-generico.js'])
    <script>
        $(function() {
            // Mostrar mensajes flash con SweetAlert2
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#28a745'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33'
                });
            @endif

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
                stateSave: false,
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
                            const totalSofiaRegistrados = Number(
                                json.sofia_registrados_filtrados ??
                                json.sofia_registrados_total ?? 0
                            );
                            $totalSofia.text(
                                `${formatNumber(totalSofiaRegistrados)} / ${formatNumber(totalFiltrado)}`
                            );

                            callback(json);
                        })
                        .catch(function(error) {
                            Swal.fire({
                                title: 'Error al cargar personas',
                                text: 'No se pudieron cargar las personas. Por favor, intente nuevamente.',
                                icon: 'error',
                                confirmButtonText: 'Entendido'
                            });
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
                    processing: [
                        '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>',
                        ' Cargando personas...'
                    ].join(''),
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
                dom: [
                    '<"row align-items-center mb-2 px-3 pt-3"',
                    '<"col-sm-12 col-md-6 mb-2 mb-md-0"l>',
                    '<"col-sm-12 col-md-6 text-md-right"f>>',
                    'rt',
                    '<"row align-items-center mt-2 px-3 pb-3"',
                    '<"col-sm-12 col-md-5 mb-2 mb-md-0"i>',
                    '<"col-sm-12 col-md-7"p>>'
                ].join('')
            });

            $estadoFilter.on('change', function() {
                personasTable.draw();
            });

            $('#btn-limpiar-filtros').on('click', function() {
                $estadoFilter.val('todos');
                personasTable.search('');
                personasTable.draw();
            });

            // Manejar eliminación con SweetAlert2
            $(document).on('submit', '.eliminar-persona-form', function(e) {
                const $form = $(this);

                // Si ya fue confirmado, permitir el envío
                if ($form.data('confirmed')) {
                    return true;
                }

                e.preventDefault();
                const personaNombre = $form.data('persona-nombre');

                // Sanitizar el nombre para prevenir XSS
                const nombreSeguro = $('<div>').text(personaNombre).html();

                Swal.fire({
                    title: '¿Estás seguro?',
                    html: `Se eliminará la persona:<br><strong>${nombreSeguro}</strong><br>` +
                        `<small class="text-danger">Esta acción también eliminará el usuario asociado</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Sí, eliminar',
                    cancelButtonText: '<i class="fas fa-times mr-1"></i> Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Marcar como confirmado y enviar
                        $form.data('confirmed', true);
                        $form.submit();
                    }
                });
            });
        });
    </script>
@endsection
