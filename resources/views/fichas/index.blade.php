@extends('adminlte::page')

@section('css')
@vite(['resources/css/parametros.css'])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@section('content_header')
<x-page-header
    icon="fa-file-alt"
    title="Fichas de Caracterización"
    subtitle="Gestión de fichas de caracterización del SENA"
    :breadcrumb="[
            ['label' => 'Inicio', 'url' => url('/'), 'icon' => 'fa-home'],
            ['label' => 'Fichas de Caracterización', 'active' => true, 'icon' => 'fa-file-alt']
        ]" />
@endsection

@section('content')
<section class="content mt-4">
    <div class="container-fluid">
        <x-session-alerts />

        <div class="row">
            <div class="col-12">
                @can('CREAR FICHA CARACTERIZACION')
                    <div class="accordion mb-4" id="accordionCrearFicha">
                        <div class="card shadow-sm no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center" id="headingCrearFicha">
                                <h2 class="mb-0 w-100">
                                    <button
                                        class="btn btn-link w-100 text-left d-flex align-items-center text-decoration-none font-weight-bold text-primary px-0"
                                        type="button" data-toggle="collapse" data-target="#collapseCrearFicha"
                                        aria-expanded="false" aria-controls="collapseCrearFicha">
                                        <i class="fas fa-plus-circle mr-2"></i> Crear Ficha de Caracterización
                                        <i class="fas fa-chevron-down ml-auto"></i>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseCrearFicha" class="collapse" aria-labelledby="headingCrearFicha"
                                data-parent="#accordionCrearFicha">
                                <div class="card-body">
                                    @include('fichas.create')
                                </div>
                            </div>
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
                    :pagination="$fichas->links()">
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
                            <strong>{{ $ficha->instructor->persona->primer_nombre ?? '' }} {{ $ficha->instructor->persona->segundo_nombre ?? '' }}</strong>
                            <br>
                            <small class="text-muted">{{ $ficha->instructor->persona->primer_apellido ?? '' }} {{ $ficha->instructor->persona->segundo_apellido ?? '' }}</small>
                            @else
                            <span class="text-muted">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-4">{{ $ficha->sede->nombre ?? 'N/A' }}</td>
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
                                    @can('VER FICHA CARACTERIZACION')
                                    <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}"
                                        class="btn btn-light btn-sm" data-toggle="tooltip"
                                        title="Ver detalles">
                                        <i class="fas fa-eye text-warning"></i>
                                    </a>
                                    @endcan
                                    @can('EDITAR FICHA CARACTERIZACION')
                                    <a href="{{ route('fichaCaracterizacion.edit', $ficha->id) }}"
                                        class="btn btn-light btn-sm" data-toggle="tooltip"
                                        title="Editar">
                                        <i class="fas fa-pencil-alt text-info"></i>
                                    </a>
                                    @endcan
                                </div>
                                <div class="btn-group btn-group-sm mb-1" role="group">
                                    @can('GESTIONAR APRENDICES FICHA')
                                    <a href="{{ route('fichaCaracterizacion.gestionarAprendices', $ficha->id) }}"
                                        class="btn btn-light btn-sm" data-toggle="tooltip"
                                        title="Gestionar Aprendices">
                                        <i class="fas fa-users text-success"></i>
                                    </a>
                                    @endcan
                                    @can('GESTIONAR INSTRUCTORES FICHA')
                                    <a href="{{ route('fichaCaracterizacion.gestionarInstructores', $ficha->id) }}"
                                        class="btn btn-light btn-sm" data-toggle="tooltip"
                                        title="Gestionar Instructores">
                                        <i class="fas fa-chalkboard-teacher text-primary"></i>
                                    </a>
                                    @endcan
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    @can('ELIMINAR FICHA CARACTERIZACION')
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
@include('layouts.footer')
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@vite(['resources/js/pages/fichas-index.js', 'resources/js/pages/fichas-form.js'])
@endsection
