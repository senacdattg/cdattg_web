@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Lista de Productos</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Productos registrados</h3>
            <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo producto
            </a>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Peso</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Código Barras</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            <td>{{ $producto->producto }}</td>
                            <td>{{ $producto->descripcion }}</td>
                            <td>{{ $producto->peso }}</td>
                            <td>{{ $producto->unidadMedida->parametro->name ?? '-' }}</td>
                            <td>{{ $producto->cantidad }}</td>
                            <td>{{ $producto->codigo_barras }}</td>
                            <td>{{ $producto->tipoProducto->parametro->name ?? '-' }}</td>
                            <td>{{ $producto->estado->parametro->name ?? '-' }}</td>
                            <td>
                                @if($producto->imagen)
                                    <img src="{{ asset($producto->imagen) }}" alt="Imagen" width="50" height="50">
                                @else
                                    <span class="text-muted">Sin imagen</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este producto?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No hay productos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
