
{{-- 
    Componente: Tabla de datos genérica
    Props:
    - $headers (array): Array de headers ['columna' => 'Título']
    - $data (collection): Datos a mostrar
    - $actions (array): Configuración de acciones ['view', 'edit', 'delete']
    - $emptyMessage (string): Mensaje cuando no hay datos
    - $emptyIcon (string): Icono para estado vacío
    - $tableClass (string): Clase adicional para la tabla
--}}
@php
    function safeValue($value) {
        if(is_null($value)) return 'N/A';
        if(is_string($value)) return $value;
        if(is_numeric($value)) return (string)$value;
        if(is_bool($value)) return $value ? 'Sí' : 'No';
        if(is_object($value)) {
            if(isset($value->name)) return $value->name;
            if(isset($value->producto)) return $value->producto;
            if(isset($value->nombre)) return $value->nombre;
            if(isset($value->parametro)) return $value->parametro;
            return 'N/A';
        }
        return 'N/A';
    }
@endphp

@props([
    'headers' => [],
    'data' => [],
    'actions' => null,
    'emptyMessage' => 'No hay datos registrados',
    'emptyIcon' => 'fas fa-inbox',
    'tableClass' => '',
    'entityType' => 'categoria'
])

@php
    $hasActions = is_array($actions) && count($actions) > 0;
@endphp

<div class="card">
    <div class="card-body p-0 table-responsive">
        <table class="table {{ $tableClass }} mb-0">
            <thead>
                <tr>
                    @foreach($headers as $key => $header)
                        <th>{{ $header }}</th>
                    @endforeach
                    @if($hasActions)
                        <th class="actions-cell text-center" style="width:180px">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    <tr>
                        @foreach($headers as $key => $header)
                            <td>
                                @if($key === '#' || $key === 'numero')
                                    <span class="badge badge-light">{{ $loop->iteration }}</span>
                                @elseif($key === 'imagen')
                                    @php
                                        $imageSrc = 'img/inventario/imagen_default.png';
                                        if(is_object($item->producto) && isset($item->producto->imagen)) {
                                            $imageSrc = $item->producto->imagen;
                                        }
                                    @endphp
                                    <img src="{{ asset($imageSrc) }}" alt="Producto" class="img-thumbnail" style="width: 50px; height: 50px;">
                                @elseif($key === 'producto' && isset($item->producto))
                                    {{ safeValue($item->producto) }}
                                @elseif($key === 'estado' || $key === 'status')
                                    @if(($item->status ?? $item->estado ?? 1) == 1)
                                        <span class="badge bg-success">ACTIVO</span>
                                    @else
                                        <span class="badge bg-danger">INACTIVO</span>
                                    @endif
                                @elseif($key === 'cantidad' || $key === 'productos_count')
                                    <span class="badge bg-info text-white">{{ $item->{$key} ?? 0 }}</span>
                                @else
                                    {{ safeValue($item->{$key} ?? null) }}
                                @endif
                            </td>
                        @endforeach
                        
                        @if($hasActions)
                            <td class="text-center actions-cell">
                                @php
                                    $entityType = $entityType ?? 'categoria';
                                    $itemId = $item->id ?? $item->categoria_id ?? $item->producto_id ?? 0;
                                    $itemName = $item->nombre ?? $item->producto ?? $item->marca ?? $item->proveedor ?? 'este elemento';
                                @endphp
                                @include('inventario._components.action-buttons', [
                                    'routeShow' => in_array('view', $actions) ? route("inventario.{$entityType}.show", $itemId) : null,
                                    'routeEdit' => in_array('edit', $actions) ? route("inventario.{$entityType}.edit", $itemId) : null,
                                    'routeDelete' => in_array('delete', $actions) ? route("inventario.{$entityType}.destroy", $itemId) : null,
                                    'itemId' => $itemId,
                                    'itemName' => $itemName
                                ])
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + ($hasActions ? 1 : 0) }}" class="text-center text-muted py-4">
                            <i class="{{ $emptyIcon }} fa-2x mb-2 d-block"></i>
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

