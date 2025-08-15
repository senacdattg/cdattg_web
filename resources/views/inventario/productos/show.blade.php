@extends('adminlte::page')

@section('content')
<div class="container_show">

    {{-- Mostrar errores de validación --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h1>Detalle del Producto</h1>
    <div class="div_show">
        <div class="col-md-8">
            <h3>{{ $producto->producto }}</h3>
            <ul class="list-group">
                <li class="list-group-item"><strong>Tipo de producto:</strong> {{ $producto->tipoProducto->parametro->name ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Descripción:</strong> {{ $producto->descripcion }}</li>
                <li class="list-group-item"><strong>Unidad de medida:</strong> {{ $producto->unidadMedida->parametro->name ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Cantidad:</strong> {{ $producto->cantidad }}</li>
                <li class="list-group-item"><strong>Código de barras:</strong> {{ $producto->codigo_barras }}</li>
                <li class="list-group-item"><strong>Estado:</strong> {{ $producto->estado->parametro->name ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Fecha de Ingreso:</strong> {{ $producto->created_at}}</li>
                
            </ul>
        </div>
        <div class="col-md-4">
            <img 
                src="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : asset('img/inventario/1755214914.png') }}" 
                alt="Imagen del producto" 
                class="img-fluid rounded shadow"
            >
        </div>
    </div>


    <a href="{{ route('productos.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
