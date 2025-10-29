
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
            // Manejar objetos ParametroTema
            if(get_class($value) === 'App\Models\ParametroTema') {
                // ParametroTema se relaciona con parametro y tema
                if($value->parametro) {
                    return $value->parametro->name ?? 'N/A';
                }
                if($value->tema) {
                    return $value->tema->name ?? 'N/A';
                }
                return 'N/A';
            }
            
            // Manejar objetos Proveedor
            if(get_class($value) === 'App\Models\Inventario\Proveedor') {
                return $value->proveedor ?? $value->name ?? 'N/A';
            }
            
            // Manejar objetos del módulo inventario
            if(get_class($value) === 'App\Models\Inventario\Categoria') {
                return $value->nombre ?? 'N/A';
            }
            if(get_class($value) === 'App\Models\Inventario\Marca') {
                return $value->nombre ?? 'N/A';
            }
            if(get_class($value) === 'App\Models\Inventario\Producto') {
                return $value->producto ?? 'N/A';
            }
            
            // Manejar relaciones dinámicas
            if(isset($value->name)) return $value->name;
            if(isset($value->producto)) return $value->producto;
            if(isset($value->nombre)) return $value->nombre;
            if(isset($value->proveedor)) {
                // Si es un objeto Proveedor, usar su nombre
                if(is_object($value->proveedor)) {
                    return $value->proveedor->proveedor ?? $value->proveedor->name ?? 'N/A';
                }
                return $value->proveedor;
            }
            if(isset($value->estado)) {
                // Si es un objeto ParametroTema (estado), usar su nombre
                if(is_object($value->estado)) {
                    if(get_class($value->estado) === 'App\Models\ParametroTema') {
                        if($value->estado->parametro) {
                            return $value->estado->parametro->name ?? 'N/A';
                        }
                        if($value->estado->tema) {
                            return $value->estado->tema->name ?? 'N/A';
                        }
                    }
                    return 'N/A';
                }
                return $value->estado;
            }
            if(isset($value->parametro)) {
                // Si es un objeto ParametroTema, usar su name
                if(is_object($value->parametro)) {
                    if(get_class($value->parametro) === 'App\Models\ParametroTema') {
                        if($value->parametro->parametro) {
                            return $value->parametro->parametro->name ?? 'N/A';
                        }
                        if($value->parametro->tema) {
                            return $value->parametro->tema->name ?? 'N/A';
                        }
                    }
                    return 'N/A';
                }
                return $value->parametro;
            }
            
            // Manejar fecha de vigencia (caso especial para contratos)
            if(isset($value->fecha_inicio) || isset($value->fecha_fin)) {
                $inicio = $value->fecha_inicio ? $value->fecha_inicio->format('d/m/Y') : 'N/A';
                $fin = $value->fecha_fin ? $value->fecha_fin->format('d/m/Y') : 'N/A';
                return $inicio . ' - ' . $fin;
            }
            
            return 'N/A';
        }
        return 'N/A';
    }
    
    function safeInteger($value) {
        if(is_null($value)) return 1; // Por defecto activo
        if(is_numeric($value)) return (int)$value;
        if(is_object($value)) {
            if(get_class($value) === 'App\Models\ParametroTema') {
                return isset($value->status) ? (int)$value->status : 1;
            }
            return 1; // Por defecto activo
        }
        return (int)$value;
    }
@endphp

@props([
    'headers' => [],
    'data' => [],
    'actions' => null,
    'emptyMessage' => 'No hay datos registrados',
    'emptyIcon' => 'fas fa-inbox',
    'tableClass' => '',
    'entityType' => 'categorias'
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
                                    @php
                                        $estadoValor = safeInteger($item->status ?? $item->estado ?? 1);
                                    @endphp
                                    @if($estadoValor == 1)
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
                                    $entityType = $entityType ?? 'categorias';
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

