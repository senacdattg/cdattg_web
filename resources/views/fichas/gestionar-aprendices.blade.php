@extends('adminlte::page')

@section('css')
    @vite(['resources/css/fichas.css'])
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
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-check mr-2"></i> Aprendices Asignados
                                <span class="badge badge-info ml-2">{{ $ficha->aprendices->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($ficha->aprendices->count() > 0)
                                <form id="form-desasignar" action="{{ route('fichaCaracterizacion.desasignarAprendices', $ficha->id) }}" method="POST">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">
                                                        <input type="checkbox" id="select-all-asignados" class="form-check-input">
                                                    </th>
                                                    <th>Documento</th>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Teléfono</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ficha->aprendices as $aprendiz)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="aprendices[]" value="{{ $aprendiz->id }}" class="form-check-input checkbox-aprendiz">
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
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <small class="text-muted">
                                            <span id="contador-seleccionados">0</span> aprendices seleccionados
                                        </small>
                                        <button type="button" id="btn-desasignar" class="btn btn-outline-danger btn-sm" disabled>
                                            <i class="fas fa-user-minus mr-1"></i> Desasignar Seleccionados
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay aprendices asignados</h5>
                                    <p class="text-muted">Utiliza el panel de la derecha para asignar aprendices a esta ficha.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Aprendices Disponibles -->
                <div class="col-md-6">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-success">
                                <i class="fas fa-user-plus mr-2"></i> Aprendices Disponibles
                                <span class="badge badge-success ml-2">{{ $aprendicesDisponibles->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($aprendicesDisponibles->count() > 0)
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

                                    <div class="table-responsive" style="max-height: 400px;">
                                        <table class="table table-hover table-sm">
                                            <thead class="thead-light sticky-top">
                                                <tr>
                                                    <th width="5%">
                                                        <input type="checkbox" id="select-all-disponibles" class="form-check-input">
                                                    </th>
                                                    <th>Documento</th>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla-aprendices">
                                                @foreach($aprendicesDisponibles as $aprendiz)
                                                    <tr class="fila-aprendiz" 
                                                        data-nombre="{{ strtolower($aprendiz->persona->primer_nombre . ' ' . $aprendiz->persona->primer_apellido) }}"
                                                        data-documento="{{ $aprendiz->persona->numero_documento }}"
                                                        data-estado="{{ $aprendiz->estado }}">
                                                        <td>
                                                            <input type="checkbox" name="aprendices[]" value="{{ $aprendiz->id }}" class="form-check-input checkbox-disponible">
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
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <small class="text-muted">
                                            <span id="contador-disponibles">0</span> aprendices seleccionados
                                        </small>
                                        <button type="button" id="btn-asignar" class="btn btn-outline-success btn-sm" disabled>
                                            <i class="fas fa-user-plus mr-1"></i> Asignar Seleccionados
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay aprendices disponibles</h5>
                                    <p class="text-muted">Todos los aprendices activos ya están asignados a fichas.</p>
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
        aprendicesSeleccionadosAsignados = $('.checkbox-aprendiz:checked').length;
        aprendicesSeleccionadosDisponibles = $('.checkbox-disponible:checked').length;
        
        $('#contador-seleccionados').text(aprendicesSeleccionadosAsignados);
        $('#contador-disponibles').text(aprendicesSeleccionadosDisponibles);
        
        $('#btn-desasignar').prop('disabled', aprendicesSeleccionadosAsignados === 0);
        $('#btn-asignar').prop('disabled', aprendicesSeleccionadosDisponibles === 0);
    }

    // Select all para aprendices asignados
    $('#select-all-asignados').change(function() {
        $('.checkbox-aprendiz').prop('checked', this.checked);
        actualizarContadores();
    });

    // Select all para aprendices disponibles
    $('#select-all-disponibles').change(function() {
        $('.checkbox-disponible').prop('checked', this.checked);
        actualizarContadores();
    });

    // Cambios en checkboxes individuales
    $('.checkbox-aprendiz, .checkbox-disponible').change(function() {
        actualizarContadores();
        
        // Actualizar select all
        const isAsignados = $(this).hasClass('checkbox-aprendiz');
        const selector = isAsignados ? '#select-all-asignados' : '#select-all-disponibles';
        const checkboxClass = isAsignados ? '.checkbox-aprendiz' : '.checkbox-disponible';
        
        const totalCheckboxes = $(checkboxClass).length;
        const checkedCheckboxes = $(checkboxClass + ':checked').length;
        
        $(selector).prop('checked', totalCheckboxes === checkedCheckboxes);
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
                $(this).find('.checkbox-disponible').prop('checked', false);
            }
        });
        
        actualizarContadores();
        $('#select-all-disponibles').prop('checked', false);
    });

    // Botón desasignar
    $('#btn-desasignar').click(function() {
        const seleccionados = $('.checkbox-aprendiz:checked').length;
        
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
