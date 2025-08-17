{{-- resources/views/inventario/salida/aprobar_salida.blade.php --}}
@extends('adminlte::page')

@vite(['resources/css/inventario/aprobar_salida.css', 'resources/js/inventario/aprobar_salida.js'])

@section('content')
<div class="aprobar-salida-container">
    <h2>Aprobar salida de orden</h2>
    <form id="formAprobarSalida">
        <div class="form-group">
            <label for="orden">N° Orden</label>
            <input type="text" id="orden" name="orden" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="producto">Producto</label>
            <input type="text" id="producto" name="producto" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad a salir</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" min="1" required>
        </div>
        <div class="form-group">
            <label for="motivo">Motivo</label>
            <textarea id="motivo" name="motivo" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn_aprobar">Aprobar salida</button>
    </form>
    <div id="mensajeAprobacion"></div>
</div>
@endsection
