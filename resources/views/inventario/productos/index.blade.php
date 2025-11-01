@extends('adminlte::page')

@section('title', 'Gestión de Productos')

@section('content_header')
    <x-page-header
        icon="fas fa-boxes"
        title="Gestión de Productos"
        subtitle="Administra los productos del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Productos', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.productos.create') }}"
                        title="Crear Producto"
                        icon="fa-plus-circle"
                        permission="CREAR PRODUCTO"
                    />

                    <x-data-table
                        title="Lista de Productos"
                        searchable="true"
                        searchAction="{{ route('inventario.productos.index') }}"
                        searchPlaceholder="Buscar producto..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '3%'],
                            ['label' => 'Producto', 'width' => '20%'],
                            ['label' => 'Código', 'width' => '10%'],
                            ['label' => 'Categoría', 'width' => '10%'],
                            ['label' => 'Marca', 'width' => '10%'],
                            ['label' => 'Cantidad', 'width' => '8%'],
                            ['label' => 'Peso', 'width' => '8%'],
                            ['label' => 'Proveedor', 'width' => '12%'],
                            ['label' => 'Estado', 'width' => '8%'],
                            ['label' => 'Opciones', 'width' => '11%', 'class' => 'text-center']
                        ]"
                        :pagination="$productos->links()"
                    >
                        @forelse ($productos as $producto)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $producto->producto }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ Str::limit($producto->descripcion, 30) ?? 'Sin descripción' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $producto->codigo_barras ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $cat = \App\Models\Parametro::find($producto->categoria_id);
                                        $esCategoria = false;
                                        if ($cat) {
                                            $esCategoria = $cat->temas()->where('name', 'CATEGORIAS')->exists();
                                        }
                                    @endphp
                                    @if($cat && $esCategoria)
                                        <span class="badge badge-info">
                                            {{ $cat->name }}
                                        </span>
                                    @elseif($cat)
                                        <span class="badge badge-warning" title="Dato incorrecto: {{ $cat->name }}">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $cat->name }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Sin categoría</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $marca = \App\Models\Parametro::find($producto->marca_id);
                                        $esMarca = false;
                                        if ($marca) {
                                            $esMarca = $marca->temas()->where('name', 'MARCAS')->exists();
                                        }
                                    @endphp
                                    @if($marca && $esMarca)
                                        <span class="badge badge-dark">
                                            {{ $marca->name }}
                                        </span>
                                    @elseif($marca)
                                        <span class="badge badge-warning" title="Dato incorrecto: {{ $marca->name }}">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $marca->name }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Sin marca</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $stockClass = 'success';
                                        if ($producto->cantidad <= 5) $stockClass = 'danger';
                                        elseif ($producto->cantidad <= 10) $stockClass = 'warning';
                                        elseif ($producto->cantidad <= 20) $stockClass = 'info';
                                    @endphp
                                    <span class="badge badge-{{ $stockClass }}">
                                        {{ $producto->cantidad }}
                                    </span>
                                </td>
                                <td>
                                    @if($producto->peso && $producto->unidadMedida)
                                        <small>{{ $producto->peso }} {{ $producto->unidadMedida->parametro->name ?? '' }}</small>
                                    @else
                                        <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        {{ $producto->contratoConvenio->name ?? $producto->contratoConvenio->proveedor->proveedor ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <x-status-badge
                                        status="{{ $producto->estado->status ?? true }}"
                                        activeText="ACTIVO"
                                        inactiveText="INACTIVO"
                                    />
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.productos.show', $producto->id) }}"
                                        editUrl="{{ route('inventario.productos.edit', $producto->id) }}"
                                        deleteUrl="{{ route('inventario.productos.destroy', $producto->id) }}"
                                        showTitle="Ver producto"
                                        editTitle="Editar producto"
                                        deleteTitle="Eliminar producto"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="10"
                                message="No hay productos registrados"
                                icon="fas fa-box"
                            />
                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />

    {{-- Alertas --}}
    @include('layout.alertas')
@endsection

@section('footer')
    {{-- Footer SENA --}}
    @include('layout.footer')
@endsection

@push('css')
    @vite([
        'public/css/inventario/shared/base.css',
    ])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/pages/formularios-generico.js'])
@endpush