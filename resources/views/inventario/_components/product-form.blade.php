@props([
    'action',
    'method' => 'POST',
    'producto' => null,
    'tiposProductos',
    'unidadesMedida',
    'estados',
    'contratosConvenios',
    'categorias',
    'marcas',
    'ambientes',
    'submitText' => 'Guardar',
    'title' => 'Registrar Producto'
])

<div class="flex_form">
    <div class="form_img_container">
        <div class="inventario-img-box">
            <img id="preview" 
                src="{{ $producto && $producto->imagen ? asset($producto->imagen) : asset('img/inventario/default.png') }}" 
                class="img-expandable">
        </div>
    </div>

    <div class="acciones_carrito">
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="form_flex">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif
            
            <div class="container_form">
                <div class="form_header">
                    <h3>{{ $title }}</h3>
                </div>
                
                <div class="row w-100">
                    <!-- Nombre -->
                    <div class="col-md-6 mb-4 form-floating">
                        <input type="text" name="producto" id="producto" class="form-control form-control-sm"
                            value="{{ old('producto', $producto->producto ?? '') }}" required placeholder="Nombre del producto">
                        <label for="producto">Nombre</label>
                    </div>

                    <!-- Tipo de producto -->
                    <div class="col-md-6 mb-4 form-floating">
                        <select name="tipo_producto_id" id="tipo_producto_id" class="form-control form-control-sm" required>
                            <option value="">Tipo de producto</option>
                            @foreach ($tiposProductos as $tipo)
                                <option value="{{ $tipo->id }}" 
                                    {{ old('tipo_producto_id', $producto->tipo_producto_id ?? '') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->parametro->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="tipo_producto_id">Tipo de producto</label>
                    </div>

                    <!-- Magnitud -->
                    <div class="col-md-6 mb-4 form-floating">
                        <input type="number" name="peso" id="peso" class="form-control form-control-sm"
                            value="{{ old('peso', $producto->peso ?? '') }}" step="0.01" min="0" required placeholder="Magnitud">
                        <label for="peso">Magnitud</label>
                    </div>

                    <!-- Unidad de medida -->
                    <div class="col-md-6 mb-4 form-floating">
                        <select name="unidad_medida_id" id="unidad_medida_id" class="form-control form-control-sm" required>
                            <option value="">Unidad de medida</option>
                            @foreach ($unidadesMedida as $unidad)
                                <option value="{{ $unidad->id }}" 
                                    {{ old('unidad_medida_id', $producto->unidad_medida_id ?? '') == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->parametro->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="unidad_medida_id">Unidad de medida</label>
                    </div>

                    <!-- Cantidad -->
                    <div class="col-md-6 mb-4 form-floating">
                        <input type="number" name="cantidad" id="cantidad" class="form-control form-control-sm"
                            value="{{ old('cantidad', $producto->cantidad ?? '') }}" required placeholder="Cantidad">
                        <label for="cantidad">Cantidad</label>
                    </div>

                    <!-- Código de barras -->
                    <div class="col-md-6 mb-4 form-floating">
                        <div class="input-group">
                            <input type="text" name="codigo_barras" id="codigo_barras" class="form-control form-control-sm"
                                value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}" required placeholder="Código de barras">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="scan-btn" title="Escanear código de barras">
                                    <i class="fas fa-scan"></i>
                                </button>
                            </div>
                        </div>
                        <label for="codigo_barras">Código de barras</label>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-6 mb-4 form-floating">
                        <select name="estado_producto_id" id="estado_producto_id" class="form-control form-control-sm" required>
                            <option value="">Estado</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id }}" 
                                    {{ old('estado_producto_id', $producto->estado_producto_id ?? '') == $estado->id ? 'selected' : '' }}>
                                    {{ $estado->parametro->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="estado_producto_id">Estado</label>
                    </div>

                    <!-- Contrato convenio -->
                    <div class="col-md-6 mb-4 form-floating">
                        <select name="contrato_convenio_id" id="contrato_convenio_id" class="form-control form-control-sm" required>
                            <option value="">Contrato convenio</option>
                            @foreach ($contratosConvenios as $contrato)
                                <option value="{{ $contrato->id }}" 
                                    {{ old('contrato_convenio_id', $producto->contrato_convenio_id ?? '') == $contrato->id ? 'selected' : '' }}>
                                    {{ $contrato->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="contrato_convenio">Contrato convenio</label>
                    </div>

                    <!-- Fecha de vencimiento -->
                    <div class="col-md-6 mb-4 form-floating">
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control form-control-sm"
                            value="{{ old('fecha_vencimiento', $producto && isset($producto->fecha_vencimiento) ? date('Y-m-d', strtotime($producto->fecha_vencimiento)) : '') }}" 
                            placeholder="">
                        <label for="fecha_vencimiento">Fecha de vencimiento</label>
                    </div>

                    <!-- Categoría -->
                    <div class="col-md-6 mb-4 form-floating">
                        <select name="categoria_id" id="categoria_id" class="form-control form-control-sm" required>
                            <option value="">Categoría</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->parametro->id }}"
                                    {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->parametro->id ? 'selected' : '' }}>
                                    {{ $categoria->parametro->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="categoria_id">Categoría</label>
                    </div>

                    <!-- Marca -->
                    <div class="col-md-6 mb-4 form-floating">
                        <select name="marca_id" id="marca_id" class="form-control form-control-sm" required>
                            <option value="">Marca</option>
                            @foreach ($marcas as $marca)
                                <option value="{{ $marca->parametro->id }}"
                                    {{ old('marca_id', $producto->marca_id ?? '') == $marca->parametro->id ? 'selected' : '' }}>
                                    {{ $marca->parametro->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="marca_id">Marca</label>
                    </div>

                    <!-- Ambiente -->
                    <div class="col-md-6 mb-4 form-floating">
                        <select name="ambiente_id" id="ambiente_id" class="form-control form-control-sm" required>
                            <option value="">Ambiente</option>
                            @foreach ($ambientes as $ambiente)
                                <option value="{{ $ambiente->id }}" 
                                    {{ old('ambiente_id', $producto->ambiente_id ?? '') == $ambiente->id ? 'selected' : '' }}>
                                    {{ $ambiente->title }}
                                </option>
                            @endforeach
                        </select>
                        <label for="ubicacion">Ambiente</label>
                    </div>

                    <!-- Imagen -->
                    <div class="col-md-12 mb-4">
                        <div class="custom-file-input-wrapper">
                            <label for="imagen" class="custom-file-input-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>{{ $producto ? 'Cambiar imagen' : 'Elegir una imagen' }}</span>
                            </label>
                            <input type="file" name="imagen" id="imagen" class="custom-file-input">
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-12 mb-4 form-floating">
                        <textarea name="descripcion" id="descripcion" class="form-control form-control-sm" 
                            style="height: 120px;" placeholder="">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                        <label for="descripcion">Descripción</label>
                    </div>
                </div>

                <div class="form_footer">
                    <button type="submit" class="btn btn-success">{{ $submitText }}</button>
                    <a href="{{ route('inventario.productos.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal para scanner de códigos de barras --}}
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scannerModalLabel">
                    <i class="fas fa-scan mr-2"></i>
                    Escanear Código de Barras
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div id="scanner-container" style="width: 100%; margin: 0 auto;">
                        <div id="qr-reader" style="width: 100%;"></div>
                    </div>
                    <div id="scanner-error" class="text-danger mt-3" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="error-message"></span>
                    </div>
                    <div id="scanner-success" class="text-success mt-3" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        <span id="success-message"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary" id="stop-scanner">
                    <i class="fas fa-stop"></i> Detener Escáner
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scanBtn = document.getElementById('scan-btn');
    const scannerModal = document.getElementById('scannerModal');
    const codigoBarrasInput = document.getElementById('codigo_barras');
    let scanner = null;
    
    // Función para iniciar el escáner
    function startScanner() {
        const html5QrCode = new Html5Qrcode("qr-reader");
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };
        
        html5QrCode.start(
            { facingMode: "environment" }, // Usar cámara trasera si está disponible
            config,
            (decodedText, decodedResult) => {
                // Código escaneado exitosamente
                codigoBarrasInput.value = decodedText;
                
                // Mostrar mensaje de éxito
                document.getElementById('scanner-success').style.display = 'block';
                document.getElementById('success-message').textContent = 'Código escaneado: ' + decodedText;
                
                // Cerrar modal después de un breve retraso
                setTimeout(() => {
                    stopScanner(html5QrCode);
                    $(scannerModal).modal('hide');
                }, 1500);
            },
            (errorMessage) => {
                // Error durante el escaneo (normal, se muestra continuamente)
                console.log('Error de escaneo:', errorMessage);
            }
        ).catch(err => {
            // Error al iniciar la cámara
            showScannerError('Error al acceder a la cámara: ' + err);
        });
        
        scanner = html5QrCode;
    }
    
    // Función para detener el escáner
    function stopScanner(html5QrCodeInstance = null) {
        if (html5QrCodeInstance) {
            html5QrCodeInstance.stop().then(() => {
                html5QrCodeInstance.clear();
            }).catch(err => {
                console.log('Error al detener escáner:', err);
            });
        } else if (scanner) {
            scanner.stop().then(() => {
                scanner.clear();
            }).catch(err => {
                console.log('Error al detener escáner:', err);
            });
        }
        
        scanner = null;
    }
    
    // Función para mostrar errores
    function showScannerError(message) {
        const errorDiv = document.getElementById('scanner-error');
        document.getElementById('error-message').textContent = message;
        errorDiv.style.display = 'block';
        
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);
    }
    
    // Event listeners
    if (scanBtn) {
        scanBtn.addEventListener('click', function() {
            // Limpiar mensajes anteriores
            document.getElementById('scanner-error').style.display = 'none';
            document.getElementById('scanner-success').style.display = 'none';
            
            // Mostrar modal
            $(scannerModal).modal('show');
            
            // Iniciar escáner después de que el modal se muestre
            setTimeout(() => {
                startScanner();
            }, 500);
        });
    }
    
    // Detener escáner cuando se cierre el modal
    $(scannerModal).on('hidden.bs.modal', function() {
        stopScanner();
        document.getElementById('scanner-error').style.display = 'none';
        document.getElementById('scanner-success').style.display = 'none';
    });
    
    // Botón para detener manualmente el escáner
    const stopBtn = document.getElementById('stop-scanner');
    if (stopBtn) {
        stopBtn.addEventListener('click', function() {
            stopScanner();
            $(scannerModal).modal('hide');
        });
    }
    
    // Verificar soporte de cámara
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        if (scanBtn) {
            scanBtn.disabled = true;
            scanBtn.title = 'Su navegador no soporta acceso a la cámara';
        }
    }
});
</script>

@include('inventario._components.image-modal')
