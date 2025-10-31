@extends('adminlte::page')

@section('title', 'Registrar Producto')

@section('content_header')
    <x-page-header
        icon="fas fa-plus-circle"
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

@section('content')
    <div class="producto-form-container fade-in">
        {{-- Alertas --}}
        @include('components.session-alerts')

        <div class="row">
            {{-- Columna de Imagen --}}
            <div class="col-lg-4 col-md-5">
                <div class="image-preview-container slide-in">
                    <div class="image-preview-box">
                        <img id="preview" 
                             src="{{ asset('img/no-image.png') }}" 
                             alt="Vista previa"
                             onerror="this.onerror=null; this.src='{{ asset('img/no-image.png') }}'">
                    </div>
                    <div class="image-upload-area">
                        <label class="image-upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Seleccionar Imagen</span>
                            <input type="file" name="imagen" id="imagen" accept="image/*">
                        </label>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> JPG, PNG (máx. 2MB)
                        </p>
                    </div>
                </div>
            </div>

            {{-- Columna de Formulario --}}
            <div class="col-lg-8 col-md-7">
                <div class="producto-form-card slide-in">
                    <div class="form-header-gradient">
                        <h3>
                            <span class="header-icon">
                                <i class="fas fa-box-open"></i>
                            </span>
                            Información del Producto
                        </h3>
                    </div>

                    <form action="{{ route('inventario.productos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-content-container">
                            {{-- Sección: Información Básica --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-info-circle"></i>
                                    Información Básica
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="producto">
                                                <i class="fas fa-tag"></i>
                                                Nombre del Producto
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control-modern @error('producto') is-invalid @enderror" 
                                                   id="producto" 
                                                   name="producto" 
                                                   value="{{ old('producto') }}"
                                                   placeholder="Ej: Laptop Dell XPS 15"
                                                   required>
                                            @error('producto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="codigo_barras">
                                                <i class="fas fa-barcode"></i>
                                                Código de Barras
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control-modern @error('codigo_barras') is-invalid @enderror" 
                                                       id="codigo_barras" 
                                                       name="codigo_barras" 
                                                       value="{{ old('codigo_barras') }}"
                                                       placeholder="Escanear o ingresar"
                                                       required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-info" id="scan-btn">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @error('codigo_barras')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label for="descripcion">
                                                <i class="fas fa-align-left"></i>
                                                Descripción
                                            </label>
                                            <textarea class="form-control-modern @error('descripcion') is-invalid @enderror" 
                                                      id="descripcion" 
                                                      name="descripcion" 
                                                      rows="3"
                                                      placeholder="Descripción detallada del producto">{{ old('descripcion') }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Clasificación --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-tags"></i>
                                    Clasificación y Tipo
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="tipo_producto_id">
                                                <i class="fas fa-cubes"></i>
                                                Tipo de Producto
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control-modern @error('tipo_producto_id') is-invalid @enderror" 
                                                    id="tipo_producto_id" 
                                                    name="tipo_producto_id" 
                                                    required>
                                                <option value="">Seleccionar tipo</option>
                                                @foreach($tiposProductos as $tipo)
                                                    <option value="{{ $tipo->id }}" {{ old('tipo_producto_id') == $tipo->id ? 'selected' : '' }}>
                                                        {{ $tipo->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tipo_producto_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="categoria_id">
                                                <i class="fas fa-folder"></i>
                                                Categoría
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control-modern @error('categoria_id') is-invalid @enderror" 
                                                    id="categoria_id" 
                                                    name="categoria_id" 
                                                    required>
                                                <option value="">Seleccionar categoría</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->parametro->id }}" {{ old('categoria_id') == $categoria->parametro->id ? 'selected' : '' }}>
                                                        {{ $categoria->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categoria_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="marca_id">
                                                <i class="fas fa-copyright"></i>
                                                Marca
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control-modern @error('marca_id') is-invalid @enderror" 
                                                    id="marca_id" 
                                                    name="marca_id" 
                                                    required>
                                                <option value="">Seleccionar marca</option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->parametro->id }}" {{ old('marca_id') == $marca->parametro->id ? 'selected' : '' }}>
                                                        {{ $marca->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('marca_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="estado_producto_id">
                                                <i class="fas fa-check-circle"></i>
                                                Estado
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control-modern @error('estado_producto_id') is-invalid @enderror" 
                                                    id="estado_producto_id" 
                                                    name="estado_producto_id" 
                                                    required>
                                                <option value="">Seleccionar estado</option>
                                                @foreach($estados as $estado)
                                                    <option value="{{ $estado->id }}" {{ old('estado_producto_id') == $estado->id ? 'selected' : '' }}>
                                                        {{ $estado->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('estado_producto_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Cantidad y Medidas --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-ruler-combined"></i>
                                    Cantidad y Medidas
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="cantidad">
                                                <i class="fas fa-boxes"></i>
                                                Cantidad
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" 
                                                   class="form-control-modern @error('cantidad') is-invalid @enderror" 
                                                   id="cantidad" 
                                                   name="cantidad" 
                                                   value="{{ old('cantidad', 0) }}"
                                                   min="0"
                                                   required>
                                            @error('cantidad')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="peso">
                                                <i class="fas fa-weight"></i>
                                                Peso/Magnitud
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" 
                                                   class="form-control-modern @error('peso') is-invalid @enderror" 
                                                   id="peso" 
                                                   name="peso" 
                                                   value="{{ old('peso') }}"
                                                   step="0.01"
                                                   min="0"
                                                   required>
                                            @error('peso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="unidad_medida_id">
                                                <i class="fas fa-balance-scale"></i>
                                                Unidad de Medida
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control-modern @error('unidad_medida_id') is-invalid @enderror" 
                                                    id="unidad_medida_id" 
                                                    name="unidad_medida_id" 
                                                    required>
                                                <option value="">Seleccionar unidad</option>
                                                @foreach($unidadesMedida as $unidad)
                                                    <option value="{{ $unidad->id }}" {{ old('unidad_medida_id') == $unidad->id ? 'selected' : '' }}>
                                                        {{ $unidad->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('unidad_medida_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Ubicación y Contratos --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Ubicación y Proveedor
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="ambiente_id">
                                                <i class="fas fa-building"></i>
                                                Ambiente
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control-modern @error('ambiente_id') is-invalid @enderror" 
                                                    id="ambiente_id" 
                                                    name="ambiente_id" 
                                                    required>
                                                <option value="">Seleccionar ambiente</option>
                                                @foreach($ambientes as $ambiente)
                                                    <option value="{{ $ambiente->id }}" {{ old('ambiente_id') == $ambiente->id ? 'selected' : '' }}>
                                                        {{ $ambiente->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ambiente_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="contrato_convenio_id">
                                                <i class="fas fa-file-contract"></i>
                                                Contrato/Convenio
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control-modern @error('contrato_convenio_id') is-invalid @enderror" 
                                                    id="contrato_convenio_id" 
                                                    name="contrato_convenio_id" 
                                                    required>
                                                <option value="">Seleccionar contrato</option>
                                                @foreach($contratosConvenios as $contrato)
                                                    <option value="{{ $contrato->id }}" {{ old('contrato_convenio_id') == $contrato->id ? 'selected' : '' }}>
                                                        {{ $contrato->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('contrato_convenio_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="fecha_vencimiento">
                                                <i class="fas fa-calendar-alt"></i>
                                                Fecha de Vencimiento
                                            </label>
                                            <input type="date" 
                                                   class="form-control-modern @error('fecha_vencimiento') is-invalid @enderror" 
                                                   id="fecha_vencimiento" 
                                                   name="fecha_vencimiento" 
                                                   value="{{ old('fecha_vencimiento') }}">
                                            @error('fecha_vencimiento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="form-actions-container">
                            <a href="{{ route('inventario.productos.index') }}" class="btn-modern btn-modern-secondary">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn-modern btn-modern-success">
                                <i class="fas fa-save"></i>
                                Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para scanner --}}
    @include('inventario._components.image-modal')
@endsection

@section('footer')
    @include('inventario._components.sena-footer')
@endsection

@push('css')
    <link href="{{ asset('css/inventario/inventario.css') }}" rel="stylesheet">
    <link href="{{ asset('css/inventario/imagen.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="{{ asset('js/inventario/imagen.js') }}"></script>
    <script>
        // Preview de imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush