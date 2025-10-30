@extends('adminlte::page')

@section('title', 'Editar Contrato/Convenio')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@section('content_header')
    <x-page-header
        icon="fas fa-edit"
        title="Editar Contrato/Convenio"
        subtitle="Modificar datos del contrato o convenio"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Contratos y Convenios', 'url' => route('inventario.contratos-convenios.index')],
            ['label' => 'Editar', 'active' => true]
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
                            <form action="{{ route('inventario.contratos-convenios.update', $contrato->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre del Contrato/Convenio <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $contrato->name) }}"
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
                                                value="{{ old('codigo', $contrato->codigo) }}"
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
                                            <label for="tipo">Tipo <span class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('tipo') is-invalid @enderror"
                                                id="tipo"
                                                name="tipo"
                                                required
                                            >
                                                <option value="">Seleccione un tipo</option>
                                                <option value="contrato" {{ old('tipo', $contrato->tipo) == 'contrato' ? 'selected' : '' }}>Contrato</option>
                                                <option value="convenio" {{ old('tipo', $contrato->tipo) == 'convenio' ? 'selected' : '' }}>Convenio</option>
                                            </select>
                                            @error('tipo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="proveedor_id">Proveedor <span class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('proveedor_id') is-invalid @enderror"
                                                id="proveedor_id"
                                                name="proveedor_id"
                                                required
                                            >
                                                <option value="">Seleccione un proveedor</option>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $contrato->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                                        {{ $proveedor->proveedor }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('proveedor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_inicio">Fecha de Inicio <span class="text-danger">*</span></label>
                                            <input
                                                type="date"
                                                class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                id="fecha_inicio"
                                                name="fecha_inicio"
                                                value="{{ old('fecha_inicio', $contrato->fecha_inicio) }}"
                                                required
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
                                                value="{{ old('fecha_fin', $contrato->fecha_fin) }}"
                                            >
                                            @error('fecha_fin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="valor">Valor</label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                class="form-control @error('valor') is-invalid @enderror"
                                                id="valor"
                                                name="valor"
                                                value="{{ old('valor', $contrato->valor) }}"
                                                placeholder="Ingrese el valor del contrato/convenio"
                                            >
                                            @error('valor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <select
                                                class="form-control @error('status') is-invalid @enderror"
                                                id="status"
                                                name="status"
                                            >
                                                <option value="1" {{ old('status', $contrato->status) == '1' ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ old('status', $contrato->status) == '0' ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="descripcion">Descripción</label>
                                            <textarea
                                                class="form-control @error('descripcion') is-invalid @enderror"
                                                id="descripcion"
                                                name="descripcion"
                                                rows="3"
                                                placeholder="Ingrese una descripción del contrato/convenio (opcional)"
                                            >{{ old('descripcion', $contrato->descripcion) }}</textarea>
                                            @error('descripcion')
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
                                                    <i class="fas fa-save mr-1"></i> Guardar Cambios
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
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/inventario/contratos_convenios.js'])
@endsection