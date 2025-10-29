@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/ordenes.css'
    ])
@endpush

@section('main-content')
    <div class="container-fluid">
            <div class="card card-ordenes">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list"></i> Listado de Órdenes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="orders-table" class="table-ordenes">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-align-left"></i> Descripción</th>
                                <th><i class="fas fa-tags"></i> Tipo</th>
                                <th><i class="fas fa-calendar-alt"></i> Fecha devolución</th>
                                <th><i class="fas fa-user"></i> Usuario</th>
                                <th><i class="fas fa-info-circle"></i> Estado</th>
                                <th><i class="fas fa-calendar-alt"></i> Creación</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                <tbody>
                    {{-- Ejemplo visual, reemplaza por tu foreach real --}}
                    {{-- @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->description ?? 'Sin descripción' }}</td>
                            <td><span class="badge badge-secondary">{{ $order->type ?? 'Salida' }}</span></td>
                            <td>{{ $order->return_date ?? 'N/A' }}</td>
                            <td><span class="badge badge-primary"><i class="fas fa-user-circle"></i> {{ $order->user->name ?? 'Usuario' }}</span></td>
                            <td><span class="badge badge-{{ $order->status == 'Aprobada' ? 'success' : ($order->status == 'Pendiente' ? 'warning' : 'secondary') }}">{{ $order->status ?? 'Pendiente' }}</span></td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('inventario.ordenes.show', $order->id) }}" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('inventario.ordenes.edit', $order->id) }}" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('inventario.ordenes.destroy', $order->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach --}}
                    <tr>
                        <td>1</td>
                        <td>Salida de reglas para ADSO</td>
                        <td><span class="badge badge-secondary">Salida</span></td>
                        <td>2025-08-25</td>
                        <td><span class="badge badge-primary"><i class="fas fa-user-circle"></i> Juan Pérez</span></td>
                        <td><span class="badge badge-success">Aprobada</span></td>
                        <td>2025-08-18</td>
                        <td>
                            <a href="#" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Salida de equipos para mantenimiento</td>
                        <td><span class="badge badge-secondary">Salida</span></td>
                        <td>2025-08-30</td>
                        <td><span class="badge badge-primary"><i class="fas fa-user-circle"></i> Ana Gómez</span></td>
                        <td><span class="badge badge-warning">Pendiente</span></td>
                        <td>2025-08-17</td>
                        <td>
                            <a href="#" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Llevarme un pc pa la casa pa jugar</td>
                        <td><span class="badge badge-secondary">Salida</span></td>
                        <td>2026-08-30</td>
                        <td><span class="badge badge-primary"><i class="fas fa-user-circle"></i> Jhon Santamaria</span></td>
                        <td><span class="badge badge-warning">Rechazada</span></td>
                        <td>2025-08-17</td>
                        <td>
                            <a href="{{ route('inventario.ordenes.index') }}" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/inventario/ordenes.js'])
@endpush
