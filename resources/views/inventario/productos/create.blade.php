@extends('adminlte::page')

@section('classes_body', 'productos-page')


@vite(['resources/css/inventario/productos.css', 'resources/js/inventario/productos.js'])


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

    <div class="row">
        <div class="col-md-4 d-flex justify-content-center align-items-start">
            <div class="inventario-img-box">
                <img id="preview" src="{{ asset('img/inventario/default.png') }}" alt="Previsualización">
            </div>
        </div>

        <div class="col-md-8">
            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="producto" class="form-label">Nombre</label>
                        <input type="text" name="producto" id="producto" class="form-control form-control-sm" value="{{ old('producto') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tipo_producto_id" class="form-label">Tipo de producto</label>
                        <select name="tipo_producto_id" id="tipo_producto_id" class="form-control form-control-sm" required>
                            <option value="">Seleccione...</option>
                            @foreach ($tiposProductos as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_producto_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->parametro->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    

                    <div class="col-md-6 mb-3">
                        <label for="peso" class="form-label">Peso</label>
                        <input type="number" name="peso" id="peso" 
                            class="form-control form-control-sm" 
                            value="{{ old('peso') }}" 
                            step="0.01" min="0" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="unidad_medida_id" class="form-label">Unidad de medida</label>
                        <select name="unidad_medida_id" id="unidad_medida_id" class="form-control form-control-sm" required>
                            <option value="">Seleccione...</option>
                            @foreach ($unidadesMedida as $unidad)
                                <option value="{{ $unidad->id }}" {{ old('unidad_medida_id') == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->parametro->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad" class="form-control form-control-sm" value="{{ old('cantidad') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="codigo_barras" class="form-label">Código de barras</label>
                        <input type="text" name="codigo_barras" id="codigo_barras" class="form-control form-control-sm" value="{{ old('codigo_barras') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="estado_id" class="form-label">Estado</label>
                        <select name="estado_id" id="estado_id" class="form-control form-control-sm" required>
                            <option value="">Seleccione...</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                    {{ $estado->parametro->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="imagen" class="form-label">Imagen</label>
                        <input type="file" name="imagen" id="imagen" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6 mb-3 descripcion-container">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" 
                                class="form-control form-control-sm" 
                                rows="4" required>{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="col-12 btn-container">
                        <button type="submit" class="btn btn-success btn-sm">Registrar Producto</button>
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection


