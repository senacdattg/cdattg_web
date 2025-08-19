@extends('adminlte::page')

@section('classes_body', 'productos-page')

@vite([
    'resources/css/inventario/productos.css',
    'resources/js/inventario/productos.js',
    'resources/css/inventario/shared/modal-imagen.css',
    'resources/js/inventario/shared/modal-imagen.js'
])

@section('content')
<div class="container inventario-container">
    @if ($errors->any())
        <div class="alert alert-danger w-100">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex_form">
        <div class="form_img_container">
            <div class="inventario-img-box">
                <img id="preview" src="{{ asset('img/inventario/default.png') }}" class="img-expandable">
            </div>
        </div>

        <div class="acciones_carrito">
            <!-- Formulario único -->
            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="form_flex">
                @csrf
                <div class="container_form">
                    <div class="form_header">
                        <h3>Registrar Producto</h3>
                    </div>
                    <div class="row w-100">
                        <div class="col-md-6 mb-4 form-floating">
                            <input type="text" name="producto" id="producto" class="form-control form-control-sm"
                                value="{{ old('producto') }}" required placeholder="">
                            <label for="producto">Nombre</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <select name="tipo_producto_id" id="tipo_producto_id" class="form-control form-control-sm" required>
                                <option value=""></option>
                                @foreach ($tiposProductos as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_producto_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->parametro->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="tipo_producto_id">Tipo de producto</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <input type="number" name="peso" id="peso" class="form-control form-control-sm"
                                value="{{ old('peso') }}" step="0.01" min="0" required placeholder="Peso">
                            <label for="peso">Magnitud</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <select name="unidad_medida_id" id="unidad_medida_id" class="form-control form-control-sm" required>
                                <option value=""></option>
                                @foreach ($unidadesMedida as $unidad)
                                    <option value="{{ $unidad->id }}" {{ old('unidad_medida_id') == $unidad->id ? 'selected' : '' }}>
                                        {{ $unidad->parametro->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="unidad_medida_id">Unidad de medida</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <input type="number" name="cantidad" id="cantidad" class="form-control form-control-sm"
                                value="{{ old('cantidad') }}" required placeholder="Cantidad">
                            <label for="cantidad">Cantidad</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <input type="text" name="codigo_barras" id="codigo_barras" class="form-control form-control-sm"
                                value="{{ old('codigo_barras') }}" required placeholder="Código de barras">
                            <label for="codigo_barras">Código de barras</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <select name="estado_id" id="estado_id" class="form-control form-control-sm" required>
                                <option value=""></option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                        {{ $estado->parametro->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="estado_id">Estado</label>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="custom-file-input-wrapper">
                                <label for="imagen" class="custom-file-input-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Elegir una imagen</span>
                                </label>
                                <input type="file" name="imagen" id="imagen" class="custom-file-input">
                            </div>
                        </div>
                        <div class="col-md-12 mb-4 form-floating descripcion-container">
                            <textarea name="descripcion" id="descripcion" class="form-control form-control-sm" rows="4" required placeholder="Descripción">{{ old('descripcion') }}</textarea>
                            <label for="descripcion">Descripción</label>
                        </div>
                        <div class="col-12 btn-container">
                            <button type="submit" class="btn btn-success btn-sm">Registrar Producto</button>
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para imagen expandida -->
<div id="modalImagen" class="modal-imagen">
    <span class="cerrar">&times;</span>
    <img class="modal-contenido" id="imgExpandida">
</div>
@endsection
