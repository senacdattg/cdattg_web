@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
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

                    <x-data-table title="Lista de Personas" :paginated="false" :searchable="false" tableId="personas-table"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre y Apellido', 'width' => '25%'],
                            ['label' => 'Número de Documento', 'width' => '20%'],
                            ['label' => 'Correo Electrónico', 'width' => '25%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Opciones', 'width' => '15%', 'class' => 'text-center'],
                        ]">
                        <tr>
                            <td colspan="6" class="text-center text-muted">Cargando registros...</td>
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
            $('#personas-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: function(data, callback) {
                    axios.get('{{ route('personas.datatable') }}', {
                            params: data
                        })
                        .then(function(response) {
                            callback(response.data);
                        })
                        .catch(function(error) {
                            console.error('Error al cargar personas:', error);
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: []
                            });
                        });
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'index',
                        name: 'index'
                    },
                    {
                        data: 'nombre',
                        name: 'nombre'
                    },
                    {
                        data: 'numero_documento',
                        name: 'numero_documento'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                ]
            });
        });
    </script>
@endsection
