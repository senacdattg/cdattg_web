@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-file-alt" 
        title="Fichas de Caracterización"
        subtitle="Gestión de fichas de caracterización del SENA"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => url('/'), 'icon' => 'fa-home'],
            ['label' => 'Fichas de Caracterización', 'active' => true, 'icon' => 'fa-file-alt']
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <x-session-alerts />

            <div class="row">
                <div class="col-12">
                    @can('CREAR PROGRAMA DE CARACTERIZACION')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <a href="{{ route('fichaCaracterizacion.create') }}" class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1 text-decoration-none">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear Ficha de Caracterización
                                </a>
                            </div>
                        </div>
                    @endcan

                    <x-data-table 
                        title="Lista de Fichas de Caracterización"
                        searchable="true"
                        searchAction="{{ route('fichaCaracterizacion.index') }}"
                        searchPlaceholder="Buscar ficha..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Ficha', 'width' => '15%'],
                            ['label' => 'Programa', 'width' => '25%'],
                            ['label' => 'Instructor Líder', 'width' => '20%'],
                            ['label' => 'Sede', 'width' => '15%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Aprendices', 'width' => '10%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                        :pagination="$fichas->links()"
                    >
                                        @forelse ($fichas as $ficha)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4">
                                                    <span class="badge badge-info">{{ $ficha->ficha }}</span>
                                                </td>
                                                <td class="px-4 font-weight-medium">
                                                    <strong>{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</strong>
                                                    @if($ficha->programaFormacion->codigo ?? false)
                                                        <br><small class="text-muted">{{ $ficha->programaFormacion->codigo }}</small>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    @if($ficha->instructor && $ficha->instructor->persona)
                                                        {{ $ficha->instructor->persona->primer_nombre }} {{ $ficha->instructor->persona->primer_apellido }}
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">{{ $ficha->sede->sede ?? 'N/A' }}</td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $ficha->status ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $ficha->status ? 'Activa' : 'Inactiva' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    @if($ficha->aprendices && $ficha->aprendices->count() > 0)
                                                        <span class="badge badge-primary">{{ $ficha->aprendices->count() }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">0</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                                        <div class="btn-group btn-group-sm mb-1" role="group">
                                                            @can('VER PROGRAMA DE CARACTERIZACION')
                                                                <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}"
                                                                    class="btn btn-light btn-sm" data-toggle="tooltip"
                                                                    title="Ver detalles">
                                                                    <i class="fas fa-eye text-warning"></i>
                                                                </a>
                                                            @endcan
                                                            @can('EDITAR PROGRAMA DE CARACTERIZACION')
                                                                <a href="{{ route('fichaCaracterizacion.edit', $ficha->id) }}"
                                                                    class="btn btn-light btn-sm" data-toggle="tooltip"
                                                                    title="Editar">
                                                                    <i class="fas fa-pencil-alt text-info"></i>
                                                                </a>
                                                            @endcan
                                                        </div>
                                                        <div class="btn-group btn-group-sm mb-1" role="group">
                                                            @can('GESTIONAR APRENDICES')
                                                                <a href="{{ route('fichaCaracterizacion.gestionarAprendices', $ficha->id) }}"
                                                                    class="btn btn-light btn-sm" data-toggle="tooltip"
                                                                    title="Gestionar Aprendices">
                                                                    <i class="fas fa-users text-success"></i>
                                                                </a>
                                                            @endcan
                                                            @can('GESTIONAR INSTRUCTORES')
                                                                <a href="{{ route('fichaCaracterizacion.gestionarInstructores', $ficha->id) }}"
                                                                    class="btn btn-light btn-sm" data-toggle="tooltip"
                                                                    title="Gestionar Instructores">
                                                                    <i class="fas fa-chalkboard-teacher text-primary"></i>
                                                                </a>
                                                            @endcan
                                                        </div>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            @can('ELIMINAR PROGRAMA DE CARACTERIZACION')
                                                                <button type="button" class="btn btn-light btn-sm" 
                                                                        data-ficha="{{ $ficha->ficha }}" 
                                                                        data-url="{{ route('fichaCaracterizacion.destroy', $ficha->id) }}"
                                                                        onclick="confirmarEliminacion(this.dataset.ficha, this.dataset.url)"
                                                                        data-toggle="tooltip" title="Eliminar">
                                                                    <i class="fas fa-trash text-danger"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data"
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay fichas de caracterización registradas</p>
                                                </td>
                                            </tr>
                                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/fichas-index.js'])
@endsection