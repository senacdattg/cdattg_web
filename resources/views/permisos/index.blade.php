@extends('adminlte::page')

{{-- Activar plugins de AdminLTE --}}
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        @media (min-width: 992px) {
            .permisos-table-responsive {
                overflow-x: visible;
            }
        }
    </style>
@endsection

@section('content_header')
    <x-page-header
        icon="fa-cogs"
        title="Permisos"
        subtitle="Gestión de permisos del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Permisos', 'icon' => 'fa-cog', 'active' => true]]"
    />
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
                                    <i class="fas fa-users-cog me-2"></i> Lista de usuarios
                                </h5>
                                <span class="text-muted small">Gestiona los permisos de manera clara y eficiente.</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <x-data-table title="Lista de Usuarios" :paginated="false" :searchable="false"
                                tableId="permisos-table" tableClass="table table-center table-sm table-striped mb-0"
                                tableWrapperClass="table-responsive permisos-table-responsive rounded-bottom"
                                :columns="[
                                    ['label' => '#', 'width' => '5%'],
                                    ['label' => 'Nombre completo', 'width' => '25%'],
                                    ['label' => 'Documento', 'width' => '15%'],
                                    ['label' => 'Correo', 'width' => '20%'],
                                    ['label' => 'Roles', 'width' => '15%'],
                                    ['label' => 'Estado', 'width' => '10%'],
                                    ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center'],
                                ]">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <span class="spinner-border spinner-border-sm mr-2" role="status"
                                            aria-hidden="true"></span>
                                        Cargando usuarios...
                                    </td>
                                </tr>
                            </x-data-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/app.js', 'resources/js/parametros.js'])
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

            const permisosTable = $('#permisos-table').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                stateSave: false,
                ajax: {
                    url: '{{ route('permiso.datatable') }}'
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json',
                    processing: [
                        '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>',
                        ' Cargando usuarios...'
                    ].join(''),
                    emptyTable: 'No se encontraron usuarios registrados.',
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
                        data: 'roles',
                        name: 'roles',
                        orderable: false,
                        searchable: false,
                        className: 'align-middle'
                    },
                    {
                        data: 'estado',
                        name: 'status',
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
        });
    </script>
@endsection
