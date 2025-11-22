/**
 * Módulo para la gestión de personas
 * Maneja DataTables, formularios y confirmaciones con SweetAlert2
 */

(function () {
    'use strict';

    // Esperar a que todos los scripts estén cargados
    function initPersonasPage() {
        // Verificar dependencias
        if (typeof $ === 'undefined' || typeof axios === 'undefined') {
            setTimeout(initPersonasPage, 100);
            return;
        }

        $(function () {
            // Las notificaciones flash se manejan globalmente en la plantilla base
            $('[data-toggle="tooltip"]').tooltip();

            // Obtener la URL del datatable desde el data attribute
            const datatableUrl = $('#personas-table').data('datatable-url');

            $(document).on('submit', '.create-user-form', function (event) {
                const $form = $(this);
                const disabledFlag = ($form.data('disabled') || '').toString() === 'true';
                const errorMessage = $form.data('error') ||
                    'Actualiza la información de correo y documento antes de crear el usuario.';

                if (disabledFlag) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No es posible crear el usuario',
                        text: errorMessage,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                if ($form.data('confirmed')) {
                    return true;
                }

                event.preventDefault();

                const personaNombre = $('<div>').text($form.data('persona-nombre') || '').html();
                const personaEmail = $('<div>').text($form.data('persona-email') || '').html();
                const personaDocumento = $('<div>').text($form.data('numero-documento') || '').html();

                Swal.fire({
                    title: 'Crear usuario',
                    html: `Se creará el usuario <strong>${personaEmail}</strong><br>` +
                        `La contraseña temporal será el número de documento: ` +
                        `<strong>${personaDocumento}</strong>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-user-plus mr-1"></i> Crear usuario',
                    cancelButtonText: '<i class="fas fa-times mr-1"></i> Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $form.data('confirmed', true);
                        $form.submit();
                    }
                });
            });

            const $estadoFilter = $('#filtro-estado');
            const $totalGeneral = $('#total-personas');
            const $totalFiltrado = $('#total-personas-filtradas');
            const $totalSofia = $('#total-personas-sofia');

            const formatNumber = function (value) {
                const numericValue = Number(value);
                return Number.isFinite(numericValue) ? numericValue.toLocaleString('es-CO') : '0';
            };

            // Función para inicializar DataTable cuando esté disponible
            function initDataTable() {
                // Verificar si DataTables está disponible (cargado por AdminLTE nativo)
                if (typeof $.fn.DataTable === 'undefined') {
                    setTimeout(initDataTable, 100);
                    return;
                }

                // Si ya existe una instancia, destruirla primero
                if ($.fn.DataTable.isDataTable('#personas-table')) {
                    $('#personas-table').DataTable().destroy();
                }

                const personasTable = $('#personas-table').DataTable({
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    stateSave: false,
                    ajax: function (data, callback) {
                        axios.get(datatableUrl, {
                            params: Object.assign({}, data, {
                                estado: $estadoFilter.val()
                            })
                        })
                            .then(function (response) {
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
                                    `${formatNumber(totalSofiaRegistrados)} / ` +
                                    `${formatNumber(totalFiltrado)}`
                                );

                                callback(json);
                            })
                            .catch(function (error) {
                                Swal.fire({
                                    title: 'Error al cargar personas',
                                    text: 'No se pudieron cargar las personas. ' +
                                        'Por favor, intente nuevamente.',
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
                            '<span class="spinner-border spinner-border-sm mr-2" ' +
                            'role="status" aria-hidden="true"></span>',
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
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'estado_sofia',
                        name: 'estado_sofia',
                        orderable: false,
                        searchable: false,
                        className: 'align-middle text-center',
                        render: function (data) {
                            return data;
                        }
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false,
                        className: 'align-middle text-center',
                        render: function (data) {
                            return data;
                        }
                    }],
                    columnDefs: [{
                        targets: 0,
                        orderable: true
                    }],
                    drawCallback: function () {
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

                $estadoFilter.off('change.datatable').on('change.datatable', function () {
                    personasTable.draw();
                });

                $('#btn-limpiar-filtros').off('click.datatable').on('click.datatable', function () {
                    $estadoFilter.val('todos');
                    personasTable.search('');
                    personasTable.draw();
                });
            }

            // Inicializar DataTable cuando el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDataTable);
            } else {
                initDataTable();
            }

            // Reinicializar cuando Livewire actualice el DOM
            if (window.Livewire) {
                document.addEventListener('livewire:navigated', function () {
                    setTimeout(initDataTable, 100);
                });
            }

            // Manejar eliminación con SweetAlert2
            $(document).on('submit', '.eliminar-persona-form', function (e) {
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
                        `<small class="text-danger">Esta acción también eliminará ` +
                        `el usuario asociado</small>`,
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

            $(document).on('submit', '.reset-password-form', function (e) {
                const $form = $(this);

                if ($form.data('confirmed')) {
                    return true;
                }

                e.preventDefault();
                const personaNombre = $form.data('persona-nombre');
                const numeroDocumento = $form.data('numero-documento');

                const nombreSeguro = $('<div>').text(personaNombre).html();
                const documentoSeguro = $('<div>').text(numeroDocumento).html();

                Swal.fire({
                    title: 'Restablecer contraseña',
                    html: '¿Deseas restablecer la contraseña de <strong>' +
                        nombreSeguro +
                        '</strong>?<br>' +
                        '<small>La nueva contraseña será su número de documento: <strong>' +
                        documentoSeguro +
                        '</strong></small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-key mr-1"></i> Sí, restablecer',
                    cancelButtonText: '<i class="fas fa-times mr-1"></i> Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $form.data('confirmed', true);
                        $form.submit();
                    }
                });
            });
        });
    }

    // Inicializar cuando el DOM y las dependencias estén listas
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPersonasPage);
    } else {
        initPersonasPage();
    }

    // Reinicializar cuando Livewire actualice el DOM
    if (window.Livewire) {
        document.addEventListener('livewire:navigated', function () {
            setTimeout(initPersonasPage, 200);
        });
    }
})();

