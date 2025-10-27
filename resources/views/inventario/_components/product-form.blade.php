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
                        <input type="text" name="codigo_barras" id="codigo_barras" class="form-control form-control-sm"
                            value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}" required placeholder="Código de barras">
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
                                <option value="{{ $categoria->id }}" 
                                    {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
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
                                <option value="{{ $marca->id }}" 
                                    {{ old('marca_id', $producto->marca_id ?? '') == $marca->id ? 'selected' : '' }}>
                                    {{ $marca->nombre }}
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

@include('inventario._components.image-modal')
