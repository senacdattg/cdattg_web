@extends('adminlte::page')

@section('title', 'Registrar Contrato/Convenio')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-plus"
        title="Registrar Contrato/Convenio"
        subtitle="Crear un nuevo contrato o convenio en el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Contratos y Convenios', 'url' => route('inventario.contratos-convenios.index')],
            ['label' => 'Registrar', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Alertas -->
            @include('components.session-alerts')

            <div class="row">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información del Contrato/Convenio
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.contratos-convenios.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre del Contrato/Convenio <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                value="{{ old('name') }}"
                                                placeholder="Ingrese el nombre del contrato o convenio"
                                                required
                                            >
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo">Código</label>
                                            <input
                                                type="text"
                                                class="form-control @error('codigo') is-invalid @enderror"
                                                id="codigo"
                                                name="codigo"
                                                value="{{ old('codigo') }}"
                                                placeholder="Ingrese el código del contrato/convenio"
                                            >
                                            @error('codigo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="proveedor_id">Proveedor</label>
                                            <select
                                                class="form-control @error('proveedor_id') is-invalid @enderror"
                                                id="proveedor_id"
                                                name="proveedor_id"
                                            >
                                                <option value="">Seleccione un proveedor</option>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                        {{ $proveedor->proveedor }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('proveedor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado_id">Estado <span class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('estado_id') is-invalid @enderror"
                                                id="estado_id"
                                                name="estado_id"
                                                required
                                            >
                                                <option value="">Seleccione un estado</option>
                                                @foreach(
                                                    \App\Models\ParametroTema::with(['parametro','tema'])
                                                        ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS'))
                                                        ->where('status', 1)
                                                        ->get() as $estado
                                                )
                                                    <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                                        {{ $estado->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('estado_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_inicio">Fecha de Inicio</label>
                                            <input
                                                type="date"
                                                class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                id="fecha_inicio"
                                                name="fecha_inicio"
                                                value="{{ old('fecha_inicio') }}"
                                            >
                                            @error('fecha_inicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_fin">Fecha de Fin</label>
                                            <input
                                                type="date"
                                                class="form-control @error('fecha_fin') is-invalid @enderror"
                                                id="fecha_fin"
                                                name="fecha_fin"
                                                value="{{ old('fecha_fin') }}"
                                            >
                                            @error('fecha_fin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card-footer bg-white py-3">
                                            <div class="action-buttons">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-save mr-1"></i> Guardar
                                                </button>
                                                <a href="{{ route('inventario.contratos-convenios.index') }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-times mr-1"></i> Cancelar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

