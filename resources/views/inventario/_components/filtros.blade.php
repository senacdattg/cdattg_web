{{-- 
    Componente: filtros.blade.php
    Componente reutilizable para filtrar órdenes por estado o tipo
    Parámetros:
    - $estado: (opcional) Filtrar por estado (EN ESPERA, APROBADA, RECHAZADA)
    - $tipo: (opcional) Filtrar por tipo (PRÉSTAMO, SALIDA)
--}}

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Filtros --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-filter"></i> Buscar Orden</label>
                                <input type="text" id="search-orden" class="form-control" placeholder="ID o descripción...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-alt"></i> Desde</label>
                                <input type="date" id="fecha-desde" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-alt"></i> Hasta</label>
                                <input type="date" id="fecha-hasta" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary btn-block" id="btn-filtrar">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de Órdenes --}}
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Listado de Órdenes
                    </h3>
                </div>
                <div class="card-body">
                    @if($ordenes && count($ordenes) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 10%">ID Orden</th>
                                        <th style="width: 15%">Usuario</th>
                                        <th style="width: 10%">Tipo</th>
                                        <th style="width: 15%">Estado</th>
                                        <th style="width: 15%">Fecha</th>
                                        <th style="width: 15%">Cantidad Items</th>
                                        <th style="width: 15%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ordenes as $orden)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $orden->id }}</span>
                                            </td>
                                            <td>{{ $orden->userCreate->name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $tipoNombre = $orden->tipoOrden->parametro->name ?? 'N/A';
                                                    $tipoClass = $tipoNombre === 'PRÉSTAMO' ? 'info' : 'warning';
                                                @endphp
                                                <span class="badge badge-{{ $tipoClass }}">
                                                    {{ $tipoNombre }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $estadoNombre = $orden->detalles->first()->estadoOrden->parametro->name ?? 'N/A';
                                                    $estadoClass = match($estadoNombre) {
                                                        'EN ESPERA' => 'warning',
                                                        'APROBADA' => 'success',
                                                        'RECHAZADA' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $estadoClass }}">
                                                    {{ $estadoNombre }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $orden->created_at ? $orden->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    {{ $orden->detalles ? count($orden->detalles) : 0 }} items
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('inventario.ordenes.show', $orden->id) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @php
                                                    $estadoActual = $orden->detalles->first()->estadoOrden->parametro->name ?? '';
                                                @endphp
                                                @if($estadoActual === 'EN ESPERA')
                                                    <a href="{{ route('inventario.ordenes.index') }}?action=edit&id={{ $orden->id }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p>No hay órdenes que coincidan con los filtros</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        @if(method_exists($ordenes, 'links'))
                            <div class="d-flex justify-content-center mt-3">
                                {{ $ordenes->links() }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            No hay órdenes para mostrar
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.getElementById('btn-filtrar').addEventListener('click', function() {
            const search = document.getElementById('search-orden').value;
            const desde = document.getElementById('fecha-desde').value;
            const hasta = document.getElementById('fecha-hasta').value;
            
            // Construir URL con parámetros
            let url = new URL(window.location.href);
            if(search) url.searchParams.set('search', search);
            if(desde) url.searchParams.set('desde', desde);
            if(hasta) url.searchParams.set('hasta', hasta);
            
            window.location.href = url.toString();
        });
    </script>
@endpush

