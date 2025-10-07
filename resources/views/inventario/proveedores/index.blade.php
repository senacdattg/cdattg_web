@extends('adminlte::page')

@section('title', 'Proveedores')
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/inventario/inventario_listas.css', 'resources/css/inventario/shared/base.css'])
@stop

@section('content_header')
    <div class="proveedores-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="mb-1">
                    <i></i>Gestión de Proveedores
                </h1>
                <p class="subtitle mb-0">Administra los proveedores del inventario</p>
            </div>
            <button type="button" class="btn_crear" data-toggle="modal" data-target="#createProveedorModal">
                <i class="fas fa-plus me-2"></i>Nuevo Proveedor
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="search-filter-container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <input type="text" id="filtro-proveedores" class="form-control" placeholder="Buscar proveedores...">
            </div>
            <div class="col-md-6 text-end">
                <span id="filter-counter" class="filter-counter"></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table proveedores-table mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Proveedor</th>
                        <th style="width:120px">NIT</th>
                        <th>Contacto</th>
                        <th style="width:90px">Contratos</th>
                        <th style="width:80px">Estado</th>
                        <th class="actions-cell text-center" style="width:160px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                        <tr>
                            <td><span class="badge badge-light">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $proveedor->proveedor }}</td>
                            <td><span class="badge badge-light">{{ $proveedor->nit ?? '—' }}</span></td>
                            <td>
                                <div class="proveedor-contacto">
                                    @if($proveedor->email)
                                        <span><i class="fas fa-envelope text-muted"></i> {{ $proveedor->email }}</span>
                                    @endif
                                    @if($proveedor->telefono)
                                        <span><i class="fas fa-phone text-muted"></i> {{ $proveedor->telefono }}</span>
                                    @endif
                                    @if(!$proveedor->email && !$proveedor->telefono)
                                        <span class="text-muted">Sin contacto</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info text-white">{{ $proveedor->contratos_convenios_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if(($proveedor->status ?? 1) == 1)
                                    <span class="badge bg-success">ACTIVO</span>
                                @else
                                    <span class="badge bg-danger">INACTIVO</span>
                                @endif
                            </td>
                            <td class="text-center actions-cell">
                                <button type="button" class="btn btn-xs btn-info" title="Ver" 
                                    onclick="viewProveedor({{ $proveedor->id }}, '{{ addslashes($proveedor->proveedor) }}', '{{ $proveedor->nit }}', '{{ $proveedor->email }}', {{ $proveedor->contratos_convenios_count ?? 0 }}, {{ $proveedor->status ?? 1 }}, '{{ $proveedor->userCreate->name ?? 'Usuario desconocido' }}', '{{ $proveedor->userUpdate->name ?? 'Usuario desconocido' }}', '{{ $proveedor->created_at?->format('d/m/Y H:i') }}', '{{ $proveedor->updated_at?->format('d/m/Y H:i') }}')"
                                    data-toggle="modal" data-target="#viewProveedorModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" title="Editar" 
                                    onclick="editProveedor({{ $proveedor->id }}, '{{ $proveedor->proveedor }}', '{{ $proveedor->nit }}', '{{ $proveedor->email }}')"
                                    data-toggle="modal" data-target="#editProveedorModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" title="Eliminar" 
                                    onclick="confirmDeleteProveedor({{ $proveedor->id }}, '{{ addslashes($proveedor->proveedor) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="fas fa-truck fa-2x mb-2 d-block"></i>
                                Sin proveedores registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación JS -->
    <div id="pagination-container" class="mt-3"></div>

    <!-- Modal Ver Proveedor -->
    <div class="modal fade" id="viewProveedorModal" tabindex="-1" aria-labelledby="viewProveedorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewProveedorModalLabel">
                        <i class="fas fa-eye me-2"></i>Detalle del Proveedor
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">ID</label>
                            <p class="form-control-plaintext" id="view_proveedor_id">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Nombre</label>
                            <p class="form-control-plaintext" id="view_proveedor_nombre">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">NIT</label>
                            <p class="form-control-plaintext" id="view_proveedor_nit">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Email</label>
                            <p class="form-control-plaintext" id="view_proveedor_email">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Contratos/Convenios</label>
                            <p class="form-control-plaintext" id="view_proveedor_contratos">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Estado</label>
                            <p class="form-control-plaintext" id="view_proveedor_estado">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Tiempo en Sistema</label>
                            <p class="form-control-plaintext" id="view_proveedor_tiempo_sistema">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Última Actividad</label>
                            <p class="form-control-plaintext" id="view_proveedor_ultima_actividad">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Creado por</label>
                            <p class="form-control-plaintext" id="view_proveedor_created_by">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Actualizado por</label>
                            <p class="form-control-plaintext" id="view_proveedor_updated_by">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Fecha de Creación</label>
                            <p class="form-control-plaintext" id="view_proveedor_created_at">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Última Actualización</label>
                            <p class="form-control-plaintext" id="view_proveedor_updated_at">-</p>
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

    <!-- Modal Crear Proveedor -->
    <div class="modal fade" id="createProveedorModal" tabindex="-1" aria-labelledby="createProveedorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createProveedorModalLabel">
                        <i class="fas fa-plus me-2"></i>Nuevo Proveedor
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventario.proveedores.store') }}" method="POST" id="createProveedorForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_proveedor" class="form-label fw-semibold">
                                    <i class="fas fa-building text-primary me-1"></i>Nombre del proveedor *
                                </label>
                                <input type="text" name="proveedor" id="create_proveedor" 
                                    class="form-control @error('proveedor') is-invalid @enderror" 
                                    placeholder="Ej: ACME Corporation..." 
                                    value="{{ old('proveedor') }}" required>
                                @error('proveedor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_nit" class="form-label fw-semibold">
                                    <i class="fas fa-id-card text-primary me-1"></i>NIT
                                </label>
                                <input type="text" name="nit" id="create_nit" 
                                    class="form-control @error('nit') is-invalid @enderror" 
                                    placeholder="123456789-0" 
                                    value="{{ old('nit') }}">
                                @error('nit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope text-primary me-1"></i>Correo electrónico
                                </label>
                                <input type="email" name="email" id="create_email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    placeholder="contacto@proveedor.com" 
                                    value="{{ old('email') }}">
                                @error('email')
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
                            <i class="fas fa-save me-1"></i>Guardar Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Proveedor -->
    <div class="modal fade" id="editProveedorModal" tabindex="-1" aria-labelledby="editProveedorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editProveedorModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Proveedor
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="editProveedorForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_proveedor" class="form-label fw-semibold">
                                    <i class="fas fa-building text-warning me-1"></i>Nombre del proveedor *
                                </label>
                                <input type="text" name="proveedor" id="edit_proveedor" 
                                    class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_nit" class="form-label fw-semibold">
                                    <i class="fas fa-id-card text-warning me-1"></i>NIT
                                </label>
                                <input type="text" name="nit" id="edit_nit" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope text-warning me-1"></i>Correo electrónico
                                </label>
                                <input type="email" name="email" id="edit_email" class="form-control">
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
    @vite(['resources/js/inventario/inventario_listas.js', 'resources/js/inventario/proveedores.js', 'resources/js/inventario/paginacion.js'])
@stop
