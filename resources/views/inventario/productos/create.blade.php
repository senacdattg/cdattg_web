@extends('adminlte::page')

@section('classes_body', 'productos-page')

@vite([
    'resources/css/inventario/shared/base.css',
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
            <form action="{{ route('inventario.productos.store') }}" method="POST" enctype="multipart/form-data" class="form_flex">
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
                                <option value="">Tipo de producto</option>
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
                                value="{{ old('peso') }}" step="0.01" min="0" required placeholder="">
                            <label for="peso">Magnitud</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <select name="unidad_medida_id" id="unidad_medida_id" class="form-control form-control-sm" required>
                                <option value="">Unidad de medida</option>
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
                                value="{{ old('cantidad') }}" required placeholder="">
                            <label for="cantidad">Cantidad</label>
                        </div>

                        <div class="col-md-6 mb-4 form-floating">
                            <input type="text" id="codigo_barras" name="codigo_barras" 
                                value="{{ old('codigo_barras') }}" required placeholder=""
                                class="form-control" autocomplete="off" autofocus>
                            <label for="codigo_barras">Código de barras</label>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const inputCodigo = document.getElementById('codigo_barras');

                                inputCodigo.addEventListener('keypress', function(event) {
                                    if (event.key === 'Enter') {
                                        event.preventDefault();

                                        const codigo = inputCodigo.value.trim();
                                        if (codigo !== '') {
                                            console.log('Código escaneado:', codigo);
                                            buscarProductoPorCodigo(codigo);
                                        }
                                    }
                                });

                                function buscarProductoPorCodigo(codigo) {
                                    fetch(`/productos/buscar/${codigo}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data) {
                                                alert(`Producto encontrado: ${data.nombre}`);
                                                document.getElementById('nombre').value = data.nombre;
                                                document.getElementById('precio').value = data.precio;
                                            } else {
                                                alert('Producto no encontrado');
                                            }
                                        })
                                        .catch(error => console.error('Error:', error));
                                }
                            });
                        </script>


                        <div class="col-md-6 mb-4 form-floating">
                            <select name="estado_producto_id" id="estado_producto_id" class="form-control form-control-sm" required>
                                <option value="">Estado</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}" {{ old('estado_producto_id') == $estado->id ? 'selected' : '' }}>
                                        {{ $estado->parametro->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="estado_producto_id">Estado</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <select name="contrato_convenio_id" id="contrato_convenio_id" class="form-control form-control-sm" required>
                                <option value="">Contrato convenio</option>
                                @foreach ($contratosConvenios as $contrato)
                                    <option value="{{ $contrato->id }}" {{ old('contrato_convenio_id') == $contrato->id ? 'selected' : '' }}>
                                        {{ $contrato->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="contrato_convenio">Contrato convenio</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control form-control-sm"
                                value="{{ old('fecha_vencimiento') }}" placeholder="">
                            <label for="fecha_vencimiento">Fecha de vencimiento</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <select name="categoria_id" id="categoria_id" class="form-control form-control-sm" required>
                                <option value="">Categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="categoria_id">Categoría</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                                <select name="marca_id" id="marca_id" class="form-control form-control-sm" required>
                                <option value="">Marca</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                        {{ $marca->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="marca_id">Marca</label>
                        </div>
                        <div class="col-md-6 mb-4 form-floating">
                            <select name="ambiente_id" id="ambiente_id" class="form-control form-control-sm" required>
                                <option value="">Ambiente</option>
                                @foreach ($ambientes as $ambiente)
                                    <option value="{{ $ambiente->id }}" {{ old('ambiente_id') == $ambiente->id ? 'selected' : '' }}>
                                        {{ $ambiente->title }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="ubicacion">Ambiente</label>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="custom-file-input-wrapper">
                                <label for="imagen" class="custom-file-input-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Elegir una imagen</span>
                                </label>
                                <input type="file" name="imagen" id="imagen" class="custom-file-input">
                            </div>
                        </div>
                        <div class="col-md-12 mb-4 form-floating">
                            <textarea name="descripcion" id="descripcion" class="form-control form-control-sm" style="height: 120px;" placeholder="">{{ old('descripcion') }}</textarea>
                            <label for="descripcion">Descripción</label>
                        </div>
                    </div>
                    <div class="form_footer">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <a href="{{ route('inventario.productos.index') }}" class="btn btn-secondary">Cancelar</a>
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
