{{-- 
    Componente: Detalle de producto (información)
    Props: $producto (object)
--}}
@props(['producto'])

<div class="show_info">
    <ul class="list-show">
        <li class="list-group-item">
            <i class="fas fa-tag"></i> 
            <strong>Producto:</strong> 
            {{ $producto->producto ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-cubes"></i> 
            <strong>Tipo de producto:</strong> 
            {{ $producto->tipoProducto->parametro->name ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-align-left"></i> 
            <strong>Descripción:</strong> 
            {{ $producto->descripcion ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-balance-scale"></i> 
            <strong>Magnitud:</strong> 
            {{ $producto->peso }} {{ $producto->unidadMedida->parametro->name ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-sort-numeric-up"></i> 
            <strong>Cantidad:</strong> 
            <span class="badge badge-{{ $producto->cantidad > 10 ? 'success' : ($producto->cantidad > 5 ? 'warning' : 'danger') }}">
                {{ $producto->cantidad }}
            </span>
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-barcode"></i> 
            <strong>Código de barras:</strong> 
            {{ $producto->codigo_barras ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-check-circle"></i> 
            <strong>Estado:</strong> 
            {{ $producto->estado->parametro->name ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-tag"></i> 
            <strong>Marca:</strong> 
            {{ $producto->marca->nombre ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-list"></i> 
            <strong>Categoría:</strong> 
            {{ $producto->categoria->nombre ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-map-marker-alt"></i> 
            <strong>Ambiente:</strong> 
            {{ $producto->ambiente->title ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-handshake"></i> 
            <strong>Contrato/Convenio:</strong> 
            {{ $producto->contratoConvenio->name ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-truck"></i> 
            <strong>Proveedor:</strong> 
            {{ $producto->contratoConvenio->proveedor->proveedor ?? 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-calendar-alt"></i> 
            <strong>Fecha de vencimiento:</strong> 
            {{ $producto->fecha_vencimiento ? $producto->fecha_vencimiento->format('d/m/Y') : 'N/A' }}
        </li>
        
        <li class="list-group-item">
            <i class="fas fa-calendar-alt"></i> 
            <strong>Fecha de Ingreso:</strong> 
            {{ $producto->created_at ? $producto->created_at->format('d/m/Y H:i') : 'N/A' }}
        </li>
        
        @if($producto->userCreate)
            <li class="list-group-item">
                <i class="fas fa-user"></i> 
                <strong>Creado por:</strong> 
                {{ $producto->userCreate->name ?? 'N/A' }}
            </li>
        @endif
        
        @if($producto->updated_at != $producto->created_at && $producto->userUpdate)
            <li class="list-group-item">
                <i class="fas fa-user-edit"></i> 
                <strong>Última actualización:</strong> 
                {{ $producto->userUpdate->name ?? 'N/A' }}
                <small class="text-muted">({{ $producto->updated_at->format('d/m/Y H:i') }})</small>
            </li>
        @endif
    </ul>
</div>
