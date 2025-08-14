@extends('adminlte::page')

@section('content')
<div class="container">
    <h1>Crear Producto</h1>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="producto" class="form-label">Nombre del producto</label>
            <input type="text" name="producto" id="producto" class="form-control" value="{{ old('producto') }}" required>
        </div>

        <div class="mb-3">
            <label for="tipo_producto_id" class="form-label">Tipo de producto</label>
            <select name="tipo_producto_id" id="tipo_producto_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach ($tiposProductos as $tipo)
                    <option value="{{ $tipo->id }}" {{ old('tipo_producto_id') == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->parametro->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" required>{{ old('descripcion') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="unidad_medida_id" class="form-label">Unidad de medida</label>
            <select name="unidad_medida_id" id="unidad_medida_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach ($unidadesMedida as $unidad)
                    <option value="{{ $unidad->id }}" {{ old('unidad_medida_id') == $unidad->id ? 'selected' : '' }}>
                        {{ $unidad->parametro->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" value="{{ old('cantidad') }}" required>
        </div>

        <div class="mb-3">
            <label for="codigo_barras" class="form-label">Codigo de barras</label>
            <input type="text" name="codigo_barras" id="codigo_barras" class="form-control" value="{{ old('codigo_barras') }}" required>
        </div>

        <div class="mb-3">
            <label for="estado_id" class="form-label">Estado</label>
            <select name="estado_id" id="estado_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                        {{ $estado->parametro->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imágen</label>
            <input type="file" name="imagen" id="imagen" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
