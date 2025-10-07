@extends('adminlte::page')

@section('title', 'Contratos y Convenios')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/inventario/inventario_listas.css', 'resources/css/inventario/shared/base.css'])
@stop

@section('content_header')
    <div class="contratos-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="mb-1">
                    <i></i>Gestión de Contratos & Convenios
                </h1>
                <p class="subtitle mb-0">Administra los contratos y convenios del inventario</p>
            </div>
            <button type="button" class="btn_crear" data-toggle="modal" data-target="#createContratoModal">
                <i class="fas fa-plus me-2"></i>Nuevo Contrato/Convenio
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="search-filter-container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <input type="text" id="filtro-contratos" class="form-control" placeholder="Buscar contratos/convenios...">
            </div>
            <div class="col-md-6 text-end">
                <span id="filter-counter" class="filter-counter"></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table contratos-table mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Nombre</th>
                        <th style="width:120px">Código</th>
                        <th style="width:110px">Fecha Inicio</th>
                        <th style="width:110px">Fecha Fin</th>
                        <th style="width:100px">Vigencia</th>
                        <th style="width:130px">Proveedor</th>
                        <th style="width:80px">Estado</th>
                        <th class="actions-cell text-center" style="width:140px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contratosConvenios as $item)
                        <tr>
                            <td><span class="badge badge-light">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $item->codigo }}</span>
                            </td>
                            <td>
                                <span class="text-sm text-muted">
                                    {{ $item->fecha_inicio ? \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-sm text-muted">
                                    {{ $item->fecha_fin ? \Carbon\Carbon::parse($item->fecha_fin)->format('d/m/Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($item->fecha_inicio && $item->fecha_fin)
                                    @php
                                        $inicio = \Carbon\Carbon::parse($item->fecha_inicio);
                                        $fin = \Carbon\Carbon::parse($item->fecha_fin);
                                        $vigencia = $inicio->diffInDays($fin);
                                    @endphp
                                    <span class="badge bg-primary text-white">{{ $vigencia }} días</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($item->proveedor)
                                    <span class="badge bg-warning text-dark">{{ $item->proveedor->proveedor }}</span>
                                @else
                                    <span class="badge bg-secondary">Sin proveedor</span>
                                @endif
                            </td>
                            <td>
                                @if($item->estado)
                                    <span class="badge bg-success">{{ $item->estado->parametro->name }}</span>
                                @else
                                    <span class="badge bg-secondary">Sin estado</span>
                                @endif
                            </td>
                            <td class="text-center actions-cell">
                                <button type="button" class="btn btn-xs btn-info" title="Ver" 
                                    onclick="viewContrato({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->codigo }}', '{{ $item->fecha_inicio }}', '{{ $item->fecha_fin }}', '{{ $item->proveedor->proveedor ?? 'Sin proveedor' }}', '{{ $item->estado->parametro->name ?? 'Sin estado' }}', '{{ $item->userCreate->name ?? 'Usuario desconocido' }}', '{{ $item->userUpdate->name ?? 'Usuario desconocido' }}', '{{ $item->created_at?->format('d/m/Y H:i') }}', '{{ $item->updated_at?->format('d/m/Y H:i') }}')"
                                    data-toggle="modal" data-target="#viewContratoModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" title="Editar" 
                                    onclick="editContrato({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->codigo }}', '{{ $item->fecha_inicio }}', '{{ $item->fecha_fin }}', {{ $item->proveedor_id ?? 'null' }}, {{ $item->estado_id ?? 'null' }})"
                                    data-toggle="modal" data-target="#editContratoModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" title="Eliminar"
                                    onclick="confirmDeleteContrato({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <i class="fas fa-file-contract fa-2x mb-2 d-block"></i>
                                Sin contratos/convenios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación JS -->
    <div id="pagination-container" class="mt-3"></div>

    <!-- Modal Ver Contrato/Convenio -->
    <div class="modal fade" id="viewContratoModal" tabindex="-1" aria-labelledby="viewContratoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewContratoModalLabel">
                        <i class="fas fa-eye me-2"></i>Detalle del Contrato/Convenio
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Nombre</label>
                            <p class="form-control-plaintext" id="view_name">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Código</label>
                            <p class="form-control-plaintext" id="view_codigo">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Proveedor</label>
                            <p class="form-control-plaintext" id="view_proveedor">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Estado</label>
                            <p class="form-control-plaintext" id="view_estado">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Fecha de Inicio</label>
                            <p class="form-control-plaintext" id="view_fecha_inicio">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Fecha de Fin</label>
                            <p class="form-control-plaintext" id="view_fecha_fin">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Vigencia</label>
                            <p class="form-control-plaintext" id="view_vigencia">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Días Restantes</label>
                            <p class="form-control-plaintext" id="view_dias_restantes">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Creado por</label>
                            <p class="form-control-plaintext" id="view_created_by">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Actualizado por</label>
                            <p class="form-control-plaintext" id="view_updated_by">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Fecha de Creación</label>
                            <p class="form-control-plaintext" id="view_created_at">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Última Actualización</label>
                            <p class="form-control-plaintext" id="view_updated_at">-</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Contrato/Convenio -->
    <div class="modal fade" id="createContratoModal" tabindex="-1" aria-labelledby="createContratoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createContratoModalLabel">
                        <i class="fas fa-plus me-2"></i>Nuevo Contrato/Convenio
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventario.contratos-convenios.store') }}" method="POST" enctype="multipart/form-data" id="createContratoForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_name" class="form-label fw-semibold">
                                    <i class="fas fa-file-contract text-primary me-1"></i>Nombre del contrato/convenio *
                                </label>
                                <input type="text" name="name" id="create_name" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    placeholder="Ej: Contrato de suministro equipos..." 
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_codigo" class="form-label fw-semibold">
                                    <i class="fas fa-barcode text-primary me-1"></i>Código *
                                </label>
                                <input type="text" name="codigo" id="create_codigo" 
                                    class="form-control @error('codigo') is-invalid @enderror" 
                                    placeholder="Ej: 045-2024" 
                                    value="{{ old('codigo') }}" required>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_fecha_inicio" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt text-primary me-1"></i>Fecha inicio
                                </label>
                                <input type="date" name="fecha_inicio" id="create_fecha_inicio" 
                                    class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                    value="{{ old('fecha_inicio') }}">
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_fecha_fin" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-times text-primary me-1"></i>Fecha fin
                                </label>
                                <input type="date" name="fecha_fin" id="create_fecha_fin" 
                                    class="form-control @error('fecha_fin') is-invalid @enderror" 
                                    value="{{ old('fecha_fin') }}">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_proveedor_id" class="form-label fw-semibold">
                                    <i class="fas fa-building text-primary me-1"></i>Proveedor
                                </label>
                                <select name="proveedor_id" id="create_proveedor_id" 
                                    class="form-control @error('proveedor_id') is-invalid @enderror">
                                    <option value="">Seleccionar proveedor...</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                            {{ $proveedor->proveedor }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('proveedor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_estado_id" class="form-label fw-semibold">
                                    <i class="fas fa-flag text-primary me-1"></i>Estado
                                </label>
                                <select name="estado_id" id="create_estado_id" 
                                    class="form-control @error('estado_id') is-invalid @enderror">
                                    <option value="">Seleccionar estado...</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                            {{ $estado->parametro->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estado_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar Contrato/Convenio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Contrato/Convenio -->
    <div class="modal fade" id="editContratoModal" tabindex="-1" aria-labelledby="editContratoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editContratoModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Contrato/Convenio
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data" id="editContratoForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label fw-semibold">
                                    <i class="fas fa-file-contract text-warning me-1"></i>Nombre del contrato/convenio *
                                </label>
                                <input type="text" name="name" id="edit_name" 
                                    class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_codigo" class="form-label fw-semibold">
                                    <i class="fas fa-barcode text-warning me-1"></i>Código *
                                </label>
                                <input type="text" name="codigo" id="edit_codigo" 
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_proveedor_id" class="form-label fw-semibold">
                                    <i class="fas fa-building text-warning me-1"></i>Proveedor
                                </label>
                                <select name="proveedor_id" id="edit_proveedor_id" class="form-control">
                                    <option value="">Seleccionar proveedor...</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->proveedor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_estado_id" class="form-label fw-semibold">
                                    <i class="fas fa-flag text-warning me-1"></i>Estado
                                </label>
                                <select name="estado_id" id="edit_estado_id" class="form-control">
                                    <option value="">Seleccionar estado...</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}">{{ $estado->parametro->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_fecha_inicio" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt text-warning me-1"></i>Fecha inicio
                                </label>
                                <input type="date" name="fecha_inicio" id="edit_fecha_inicio" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_fecha_fin" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-times text-warning me-1"></i>Fecha fin
                                </label>
                                <input type="date" name="fecha_fin" id="edit_fecha_fin" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        @if(session('success'))
            window.flashSuccess = @json(session('success'));
        @endif
        @if(session('error'))
            window.flashError = @json(session('error'));
        @endif
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/inventario/inventario_listas.js', 'resources/js/inventario/contratos_convenios.js', 'resources/js/inventario/paginacion.js'])
@stop
