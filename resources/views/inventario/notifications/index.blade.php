@extends('inventario.layouts.base')

@section('title', 'Notificaciones de Inventario')

@section('content_header')
    <x-page-header
        icon="fas fa-bell"
        title="Notificaciones de Inventario"
        subtitle="Gestiona todas las notificaciones del módulo de inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '/home'],
            ['label' => 'Inventario', 'url' => '/inventario/dashboard'],
            ['label' => 'Notificaciones', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bell mr-2"></i>
                            Todas las Notificaciones
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($notifications->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Mensaje</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notifications as $notification)
                                            <tr class="{{ is_null($notification->read_at) ? 'table-warning' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if(is_null($notification->read_at))
                                                            <i class="fas fa-circle text-warning mr-2" style="font-size: 8px;"></i>
                                                        @endif
                                                        <span>{{ $notification->message }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $notification->created_at->format('d/m/Y H:i') }}
                                                        <br>
                                                        <em>{{ $notification->created_at->diffForHumans() }}</em>
                                                    </small>
                                                </td>
                                                <td>
                                                    @if(is_null($notification->read_at))
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-envelope"></i> No leída
                                                        </span>
                                                    @else
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> Leída
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(is_null($notification->read_at))
                                                        <form action="{{ route('inventario.notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-check"></i> Marcar como leída
                                                            </button>
                                                        </form>
                                                    @else
                                                        <small class="text-muted">Leída el {{ $notification->read_at->format('d/m/Y H:i') }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No hay notificaciones</h4>
                                <p class="text-muted">Cuando haya actividad en el inventario, recibirás notificaciones aquí.</p>
                            </div>
                        @endif
                    </div>
                    @if($notifications->hasPages())
                        <div class="card-footer">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
