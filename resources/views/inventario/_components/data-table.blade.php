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
@props([
    'headers' => [],
    'data' => [],
    'actions' => ['view', 'edit', 'delete'],
    'emptyMessage' => 'No hay datos registrados',
    'emptyIcon' => 'fas fa-inbox',
    'tableClass' => ''
])

<div class="card">
    <div class="card-body p-0 table-responsive">
        <table class="table {{ $tableClass }} mb-0">
            <thead>
                <tr>
                    @foreach($headers as $key => $header)
                        <th>{{ $header }}</th>
                    @endforeach
                    @if(count($actions) > 0)
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
                                @elseif($key === 'estado' || $key === 'status')
                                    @if(($item->status ?? $item->estado ?? 1) == 1)
                                        <span class="badge bg-success">ACTIVO</span>
                                    @else
                                        <span class="badge bg-danger">INACTIVO</span>
                                    @endif
                                @elseif($key === 'cantidad' || $key === 'productos_count')
                                    <span class="badge bg-info text-white">{{ $item->{$key} ?? 0 }}</span>
                                @else
                                    {{ $item->{$key} ?? 'N/A' }}
                                @endif
                            </td>
                        @endforeach
                        
                        @if(count($actions) > 0)
                            <td class="text-center actions-cell">
                                @if(in_array('view', $actions))
                                    <button type="button" class="btn btn-xs btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @endif
                                
                                @if(in_array('edit', $actions))
                                    <button type="button" class="btn btn-xs btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                                
                                @if(in_array('delete', $actions))
                                    <button type="button" class="btn btn-xs btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + (count($actions) > 0 ? 1 : 0) }}" class="text-center text-muted py-4">
                            <i class="{{ $emptyIcon }} fa-2x mb-2 d-block"></i>
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

