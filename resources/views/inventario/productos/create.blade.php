
@extends('inventario.layouts.form')

@section('title', 'Registrar Producto')

@section('content_header')
    <x-page-header
        icon="fas fa-plus"
        title="Registrar Producto"
        subtitle="Crear un nuevo producto en el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Productos', 'url' => route('inventario.productos.index')],
            ['label' => 'Registrar', 'active' => true]
        ]"
    />
@endsection

@push('css')
    @vite(['resources/css/style.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
@endpush

<div class="container-fluid">
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Alertas -->
            @include('components.session-alerts')

            <div class="row">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información del Producto
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.productos.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- Campos del producto -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="producto">Nombre del Producto <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control @error('producto') is-invalid @enderror"
                                                id="producto"
                                                name="producto"
                                                value="{{ old('producto') }}"
                                                placeholder="Ingrese el nombre del producto"
                                                required
                                            >
                                            @error('producto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo_barras">Código de Barras</label>
                                            <div class="input-group">
                                                <input
                                                    type="text"
                                                    class="form-control @error('codigo_barras') is-invalid @enderror"
                                                    id="codigo_barras"
                                                    name="codigo_barras"
                                                    value="{{ old('codigo_barras') }}"
                                                    placeholder="Ingrese o escanee el código de barras"
                                                >
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-info" id="btn-scanner">
                                                        <i class="fas fa-qrcode"></i> Escanear
                                                    </button>
                                                </div>
                                            </div>
                                            @error('codigo_barras')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="categoria_id">Categoría <span class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('categoria_id') is-invalid @description"
                                                id="categoria_id"
                                                name="categoria_id"
                                                required
                                            >
                                                <option value="">Seleccione una categoría</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                        {{ $categoria->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categoria_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="marca_id">Marca <span class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('marca_id') is-invalid @enderror"
                                                id="marca_id"
                                                name="marca_id"
                                                required
                                            >
                                                <option value="">Seleccione una marca</option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                                        {{ $marca->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('marca_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Otros campos necesarios -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
                                            <input
                                                type="number"
                                                class="form-control @error('cantidad') is-invalid @enderror"
                                                id="cantidad"
                                                name="cantidad"
                                                value="{{ old('cantidad', 0) }}"
                                                min="0"
                                                required
                                            >
                                            @error('cantidad')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="peso">Peso</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                class="form-control @error('peso') is-invalid @enderror"
                                                id="peso"
                                                name="peso"
                                                value="{{ old('peso') }}"
                                                placeholder="Ingrese el peso del producto"
                                            >
                                            @error('peso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="descripcion">Descripción</label>
                                            <textarea
                                                class="form-control @error('descripcion') is-invalid @enderror"
                                                id="descripcion"
                                                name="descripcion"
                                                rows="3"
                                                placeholder="Ingrese una descripción del producto (opcional)"
                                            >{{ old('descripcion') }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card-footer bg-white py-3">
                                            <div class="action-buttons">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-save mr-1"></i> Guardar
                                                </button>
                                                <a href="{{ route('inventario.productos.index') }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-times mr-1"></i> Cancelar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal del Scanner -->
<div class="modal fade" id="scannerModal" tabindex="-1" role="dialog" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scannerModalLabel">
                    <i class="fas fa-qrcode mr-2"></i>
                    Escáner de Código de Barras
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="qr-reader" style="width: 100%;"></div>
                        <div id="qr-reader-results"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <p><strong>Instrucciones:</strong></p>
                            <ul class="text-left">
                                <li>Permita el acceso a la cámara</li>
                                <li>Apunte el código de barras hacia la cámara</li>
                                <li>El código se llenará automáticamente</li>
                            </ul>
                            <div id="scan-status" class="alert alert-info mt-3">
                                <i class="fas fa-camera mr-2"></i>
                                Iniciando cámara...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" id="stopScanner" class="btn btn-warning">
                    <i class="fas fa-stop mr-1"></i> Detener Scanner
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Configuración del scanner
        let html5QrcodeScanner = null;
        
        $('#btn-scanner').click(function() {
            $('#scannerModal').modal('show');
            startScanner();
        });
        
        $('#scannerModal').on('hidden.bs.modal', function() {
            stopScanner();
        });
        
        $('#stopScanner').click(function() {
            stopScanner();
            $('#scannerModal').modal('hide');
        });
        
        function startScanner() {
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };
            
            html5QrcodeScanner = new Html5Qrcode("qr-reader");
            
            html5QrcodeScanner.start(
                { facingMode: "environment" }, // Usar cámara trasera
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                $('#scan-status').removeClass('alert-info').addClass('alert-success').html('<i class="fas fa-check mr-2"></i>¡Scanner listo! Apunte el código de barras.');
            }).catch(err => {
                $('#scan-status').removeClass('alert-info').addClass('alert-danger').html('<i class="fas fa-exclamation-triangle mr-2"></i>Error al iniciar la cámara: ' + err);
            });
        }
        
        function stopScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner.clear();
                    $('#scan-status').removeClass('alert-success alert-danger').addClass('alert-info').html('<i class="fas fa-camera mr-2"></i>Scanner detenido.');
                }).catch(err => {
                    console.error("Error stopping scanner:", err);
                });
            }
        }
        
        function onScanSuccess(decodedText, decodedResult) {
            // Llenar el campo de código de barras
            $('#codigo_barras').val(decodedText);
            
            // Mostrar mensaje de éxito
            Swal.fire({
                title: '¡Código Escaneado!',
                text: 'Código: ' + decodedText,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Cerrar el modal
            $('#scannerModal').modal('hide');
        }
        
        function onScanError(error) {
            // Opcional: mostrar errores de escaneo (se ejecuta frecuentemente)
            // console.log("Scan error:", error);
        }
    </script>
    @vite([
        'resources/js/inventario/productos.js',
        'resources/js/inventario/shared/modal-imagen.js'
    ])
@endsection

