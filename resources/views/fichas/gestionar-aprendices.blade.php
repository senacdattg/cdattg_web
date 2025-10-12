@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        /* === ESTILOS LIMPIOS PARA GESTIONAR APRENDICES === */
        
        /* Layout principal */
        .content {
            min-height: calc(100vh - 200px);
            padding-bottom: 20px;
        }
        
        .container-fluid {
            max-width: 100%;
            padding: 0 15px;
        }
        
        /* Cards */
        .detail-card {
            border: none;
            border-radius: 0.375rem;
            height: fit-content;
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .detail-card .card-header {
            border-bottom: 1px solid #dee2e6;
            border-radius: 0.375rem 0.375rem 0 0;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        /* Bordes superiores de colores */
        .border-top-primary {
            border-top: 3px solid #007bff !important;
        }
        
        .border-top-success {
            border-top: 3px solid #28a745 !important;
        }
        
        .text-primary {
            color: #007bff !important;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        /* === TABLAS CON SCROLL FUNCIONAL === */
        
        .table-container {
            padding: 1rem 1rem 0 1rem;
            position: relative;
        }
        
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            width: 100%;
        }
        
        /* Scrollbars personalizados */
        .table-responsive::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 6px;
            border: 2px solid #f1f1f1;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        .table-responsive::-webkit-scrollbar-corner {
            background: #f1f1f1;
        }
        
        /* Tabla con ancho mínimo para scroll horizontal */
        .table {
            width: 100%;
            min-width: 800px;
            margin-bottom: 0;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        
        /* Headers sticky */
        .table-header {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }
        
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            white-space: nowrap;
        }
        
        .table td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
            white-space: nowrap;
        }
        
        /* Columnas específicas */
        .table th:nth-child(1), .table td:nth-child(1) { width: 60px; } /* Checkbox */
        .table th:nth-child(2), .table td:nth-child(2) { width: 120px; } /* Documento */
        .table th:nth-child(3), .table td:nth-child(3) { width: 250px; min-width: 200px; white-space: normal; } /* Nombre */
        .table th:nth-child(4), .table td:nth-child(4) { width: 200px; } /* Email */
        .table th:nth-child(5), .table td:nth-child(5) { width: 120px; } /* Teléfono */
        .table th:nth-child(6), .table td:nth-child(6) { width: 100px; } /* Estado */
        
        /* === CHECKBOXES RESTAURADOS === */
        
        /* Forzar estilos específicos para checkboxes en esta vista */
        .card-body .form-check-input {
            width: 18px !important;
            height: 18px !important;
            margin: 0 !important;
            cursor: pointer;
            border-radius: 3px !important;
            border: 2px solid #ced4da !important;
            background-color: #fff !important;
            vertical-align: middle !important;
            position: relative !important;
            display: inline-block !important;
            flex-shrink: 0 !important;
        }
        
        .card-body .form-check-input:checked {
            background-color: #007bff !important;
            border-color: #007bff !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e") !important;
        }
        
        /* Celdas de checkbox perfectamente centradas y contenidas */
        .card-body td:first-child, 
        .card-body th:first-child {
            text-align: center !important;
            vertical-align: middle !important;
            width: 60px !important;
            min-width: 60px !important;
            max-width: 60px !important;
            padding: 0.5rem !important;
            overflow: hidden !important;
            position: relative !important;
        }
        
        /* Asegurar que las celdas de checkbox no se salgan del contenedor */
        .card-body .table td:first-child,
        .card-body .table th:first-child {
            box-sizing: border-box !important;
            white-space: nowrap !important;
        }
        
        /* Asegurar que los checkboxes estén contenidos dentro de sus celdas */
        .card-body .table td:first-child .form-check-input {
            position: relative !important;
            left: 0 !important;
            right: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            transform: none !important;
            float: none !important;
            clear: none !important;
        }
        
        /* Prevenir que los checkboxes se salgan del contenedor de la tabla */
        .table-container {
            contain: layout !important;
        }
        
        .card-body {
            contain: layout !important;
            overflow: hidden !important;
        }
        
        /* Forzar que las celdas mantengan su tamaño */
        .card-body .table td:first-child,
        .card-body .table th:first-child {
            width: 60px !important;
            min-width: 60px !important;
            max-width: 60px !important;
            flex: none !important;
            flex-grow: 0 !important;
            flex-shrink: 0 !important;
            flex-basis: 60px !important;
        }
        
        /* === HOVER Y TRANSICIONES === */
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transition: background-color 0.15s ease-in-out;
        }
        
        /* === BOTONES === */
        
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .btn-outline-success:hover {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .btn.d-flex {
            gap: 0.5rem;
        }
        
        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }
        
        /* === BADGES === */
        
        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        /* === RESPONSIVE === */
        
        .col-md-6 {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        @media (max-width: 768px) {
            .col-md-6 {
                padding-left: 5px;
                padding-right: 5px;
                margin-bottom: 15px;
            }
        }
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-users text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Gestionar Aprendices</h1>
                        <p class="text-muted mb-0 font-weight-light">Ficha: {{ $ficha->ficha }} - {{ $ficha->programaFormacion->nombre ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('fichaCaracterizacion.index') }}" class="link_right_header">
                                    <i class="fas fa-file-alt"></i> Fichas de Caracterización
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="link_right_header">
                                    <i class="fas fa-eye"></i> {{ $ficha->ficha }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-users"></i> Gestionar Aprendices
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('fichaCaracterizacion.show', $ficha->id) }}">
                <i class="fas fa-arrow-left mr-1"></i> Volver a la Ficha
            </a>

            <div class="row">
                <!-- Aprendices Asignados -->
                <div class="col-md-6">
                    <div class="card detail-card no-hover shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-top-primary">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-check mr-2"></i> Personas Asignadas como Aprendices
                            </h5>
                            <div class="badge badge-info">
                                {{ $ficha->aprendices->count() }}
                            </div>
                        </div>
                        <div class="card-body table-container">
                            @if($ficha->aprendices->count() > 0)
                                <form id="form-desasignar" action="{{ route('fichaCaracterizacion.desasignarAprendices', $ficha->id) }}" method="POST">
                                    @csrf
                                    
                                    <!-- Filtros para aprendices asignados -->
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <input type="text" id="filtro-asignados" class="form-control form-control-sm" placeholder="Buscar por nombre o documento...">
                                        </div>
                                        <div class="col-md-4">
                                            <select id="filtro-estado-asignados" class="form-control form-control-sm">
                                                <option value="">Todos los estados</option>
                                                <option value="1">Activos</option>
                                                <option value="0">Inactivos</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-header">
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="select-all-asignados" class="form-check-input" title="Seleccionar todos">
                                                    </th>
                                                    <th>Documento</th>
                                                    <th>Nombre Completo</th>
                                                    <th>Correo Electrónico</th>
                                                    <th>Teléfono</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla-asignados">
                                                @foreach($ficha->aprendices as $aprendiz)
                                                    <tr class="table-row fila-asignado" 
                                                        data-nombre="{{ strtolower($aprendiz->persona->primer_nombre . ' ' . $aprendiz->persona->primer_apellido) }}"
                                                        data-documento="{{ $aprendiz->persona->numero_documento }}"
                                                        data-estado="{{ $aprendiz->estado }}">
                                                        <td>
                                                            <input type="checkbox" name="personas[]" value="{{ $aprendiz->persona->id }}" class="form-check-input checkbox-asignado" id="asignado-{{ $aprendiz->persona->id }}">
                                                        </td>
                                                        <td>{{ $aprendiz->persona->numero_documento }}</td>
                                                        <td>
                                                            <strong>
                                                                {{ $aprendiz->persona->primer_nombre }} {{ $aprendiz->persona->primer_apellido }}
                                                                @if($aprendiz->persona->segundo_nombre)
                                                                    {{ $aprendiz->persona->segundo_nombre }}
                                                                @endif
                                                                @if($aprendiz->persona->segundo_apellido)
                                                                    {{ $aprendiz->persona->segundo_apellido }}
                                                                @endif
                                                            </strong>
                                                        </td>
                                                        <td>{{ $aprendiz->persona->email ?? 'N/A' }}</td>
                                                        <td>{{ $aprendiz->persona->telefono ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($aprendiz->estado)
                                                                <span class="badge badge-success">Activo</span>
                                                            @else
                                                                <span class="badge badge-danger">Inactivo</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                                
                                <!-- Botones fuera del contenedor de tabla -->
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="padding: 1rem;">
                                    <small class="text-muted">
                                        <span id="contador-seleccionados">0</span> personas seleccionadas
                                    </small>
                                    <button type="button" id="btn-desasignar" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2" disabled>
                                        <i class="fas fa-user-minus"></i> Desasignar Seleccionadas
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay personas asignadas como aprendices</h5>
                                    <p class="text-muted">Utiliza el panel de la derecha para asignar personas como aprendices a esta ficha.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Aprendices Disponibles -->
                <div class="col-md-6">
                    <div class="card detail-card no-hover shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-top-success">
                            <h5 class="card-title m-0 font-weight-bold text-success">
                                <i class="fas fa-user-plus mr-2"></i> Personas Disponibles (Sin Rol APRENDIZ)
                            </h5>
                            <div class="badge badge-success">
                                {{ $personasDisponibles->count() }}
                            </div>
                        </div>
                        <div class="card-body table-container">
                            @if($personasDisponibles->count() > 0)
                                <form id="form-asignar" action="{{ route('fichaCaracterizacion.asignarAprendices', $ficha->id) }}" method="POST">
                                    @csrf
                                    
                                    <!-- Filtros -->
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <input type="text" id="filtro-nombre" class="form-control form-control-sm" placeholder="Buscar por nombre o documento...">
                                        </div>
                                        <div class="col-md-4">
                                            <select id="filtro-estado" class="form-control form-control-sm">
                                                <option value="">Todos los estados</option>
                                                <option value="1">Activos</option>
                                                <option value="0">Inactivos</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-header">
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="select-all-disponibles" class="form-check-input" title="Seleccionar todos">
                                                    </th>
                                                    <th>Documento</th>
                                                    <th>Nombre Completo</th>
                                                    <th>Correo Electrónico</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla-aprendices">
                                                @foreach($personasDisponibles as $persona)
                                                    <tr class="fila-aprendiz table-row" 
                                                        data-nombre="{{ strtolower($persona->primer_nombre . ' ' . $persona->primer_apellido) }}"
                                                        data-documento="{{ $persona->numero_documento }}"
                                                        data-estado="{{ $persona->status }}">
                                                        <td>
                                                            <input type="checkbox" name="personas[]" value="{{ $persona->id }}" class="form-check-input checkbox-disponible" id="disponible-{{ $persona->id }}">
                                                        </td>
                                                        <td>{{ $persona->numero_documento }}</td>
                                                        <td>
                                                            <strong>
                                                                {{ $persona->primer_nombre }} {{ $persona->primer_apellido }}
                                                                @if($persona->segundo_nombre)
                                                                    {{ $persona->segundo_nombre }}
                                                                @endif
                                                                @if($persona->segundo_apellido)
                                                                    {{ $persona->segundo_apellido }}
                                                                @endif
                                                            </strong>
                                                        </td>
                                                        <td>{{ $persona->email ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($persona->status)
                                                                <span class="badge badge-success">Activo</span>
                                                            @else
                                                                <span class="badge badge-danger">Inactivo</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                                
                                <!-- Botones fuera del contenedor de tabla -->
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="padding: 1rem;">
                                    <small class="text-muted">
                                        <span id="contador-disponibles">0</span> personas seleccionadas
                                    </small>
                                    <button type="button" id="btn-asignar" class="btn btn-outline-success btn-sm d-flex align-items-center gap-2" disabled>
                                        <i class="fas fa-user-plus"></i> Asignar como Aprendices
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay personas disponibles</h5>
                                    <p class="text-muted">Todas las personas ya tienen el rol de APRENDIZ o están asignadas a fichas.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Variables globales
    let aprendicesSeleccionadosAsignados = 0;
    let aprendicesSeleccionadosDisponibles = 0;

    // Función para actualizar contadores
    function actualizarContadores() {
        aprendicesSeleccionadosAsignados = $('.checkbox-asignado:checked').length;
        aprendicesSeleccionadosDisponibles = $('.checkbox-disponible:checked').length;
        
        $('#contador-seleccionados').text(aprendicesSeleccionadosAsignados);
        $('#contador-disponibles').text(aprendicesSeleccionadosDisponibles);
        
        $('#btn-desasignar').prop('disabled', aprendicesSeleccionadosAsignados === 0);
        $('#btn-asignar').prop('disabled', aprendicesSeleccionadosDisponibles === 0);
    }

    // Select all para aprendices asignados
    $('#select-all-asignados').change(function() {
        $('.checkbox-asignado').prop('checked', this.checked);
        actualizarContadores();
    });

    // Select all para aprendices disponibles
    $('#select-all-disponibles').change(function() {
        $('.checkbox-disponible').prop('checked', this.checked);
        actualizarContadores();
    });

    // Cambios en checkboxes individuales
    $('.checkbox-asignado, .checkbox-disponible').change(function() {
        actualizarContadores();
        
        // Actualizar select all
        const isAsignados = $(this).hasClass('checkbox-asignado');
        const selector = isAsignados ? '#select-all-asignados' : '#select-all-disponibles';
        const checkboxClass = isAsignados ? '.checkbox-asignado' : '.checkbox-disponible';
        
        const totalCheckboxes = $(checkboxClass).length;
        const checkedCheckboxes = $(checkboxClass + ':checked').length;
        
        $(selector).prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Filtros para aprendices asignados
    $('#filtro-asignados, #filtro-estado-asignados').on('input change', function() {
        const filtroNombre = $('#filtro-asignados').val().toLowerCase();
        const filtroEstado = $('#filtro-estado-asignados').val();
        
        $('.fila-asignado').each(function() {
            const nombre = $(this).data('nombre');
            const documento = $(this).data('documento');
            const estado = $(this).data('estado').toString();
            
            const coincideNombre = nombre.includes(filtroNombre) || documento.includes(filtroNombre);
            const coincideEstado = filtroEstado === '' || estado === filtroEstado;
            
            if (coincideNombre && coincideEstado) {
                $(this).show();
            } else {
                $(this).hide();
                // No desmarcar checkboxes al ocultar filas
            }
        });
        
        actualizarContadores();
        $('#select-all-asignados').prop('checked', false);
    });

    // Filtros para aprendices disponibles
    $('#filtro-nombre, #filtro-estado').on('input change', function() {
        const filtroNombre = $('#filtro-nombre').val().toLowerCase();
        const filtroEstado = $('#filtro-estado').val();
        
        $('.fila-aprendiz').each(function() {
            const nombre = $(this).data('nombre');
            const documento = $(this).data('documento');
            const estado = $(this).data('estado').toString();
            
            const coincideNombre = nombre.includes(filtroNombre) || documento.includes(filtroNombre);
            const coincideEstado = filtroEstado === '' || estado === filtroEstado;
            
            if (coincideNombre && coincideEstado) {
                $(this).show();
            } else {
                $(this).hide();
                // No desmarcar checkboxes al ocultar filas
            }
        });
        
        actualizarContadores();
        $('#select-all-disponibles').prop('checked', false);
    });

    // Botón desasignar
    $('#btn-desasignar').click(function() {
        const seleccionados = $('.checkbox-asignado:checked').length;
        
        if (seleccionados === 0) {
            Swal.fire('Error', 'Debes seleccionar al menos un aprendiz para desasignar.', 'error');
            return;
        }

        Swal.fire({
            title: '¿Confirmar desasignación?',
            text: `¿Estás seguro de desasignar ${seleccionados} aprendiz(es) de esta ficha?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, desasignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form-desasignar').submit();
            }
        });
    });

    // Botón asignar
    $('#btn-asignar').click(function() {
        const seleccionados = $('.checkbox-disponible:checked').length;
        
        if (seleccionados === 0) {
            Swal.fire('Error', 'Debes seleccionar al menos un aprendiz para asignar.', 'error');
            return;
        }

        Swal.fire({
            title: '¿Confirmar asignación?',
            text: `¿Estás seguro de asignar ${seleccionados} aprendiz(es) a esta ficha?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form-asignar').submit();
            }
        });
    });

    // Inicializar contadores
    actualizarContadores();
});
</script>
@endsection
