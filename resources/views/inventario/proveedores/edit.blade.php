@extends('adminlte::page')

@section('title', 'Editar Proveedor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@section('content_header')
    <x-page-header
        icon="fas fa-edit"
        title="Editar Proveedor"
        subtitle="Modificar datos del proveedor"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Proveedores', 'url' => route('inventario.proveedores.index')],
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
                                Información del Proveedor
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.proveedores.update', $proveedor->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="proveedor">Nombre del Proveedor <span class="text-danger">*</span></label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('proveedor') is-invalid @enderror" 
                                                id="proveedor" 
                                                name="proveedor" 
                                                value="{{ old('proveedor', $proveedor->proveedor) }}" 
                                                placeholder="Ingrese el nombre del proveedor"
                                                required
                                            >
                                            @error('proveedor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nit">NIT</label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('nit') is-invalid @enderror" 
                                                id="nit" 
                                                name="nit" 
                                                value="{{ old('nit', $proveedor->nit) }}" 
                                                placeholder="Ingrese el NIT del proveedor"
                                            >
                                            @error('nit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Correo Electrónico</label>
                                            <input 
                                                type="email" 
                                                class="form-control @error('email') is-invalid @enderror" 
                                                id="email" 
                                                name="email" 
                                                value="{{ old('email', $proveedor->email) }}" 
                                                placeholder="Ingrese el correo electrónico"
                                            >
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono">Teléfono</label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('telefono') is-invalid @enderror" 
                                                id="telefono" 
                                                name="telefono" 
                                                value="{{ old('telefono', $proveedor->telefono) }}" 
                                                placeholder="Ingrese el teléfono"
                                            >
                                            @error('telefono')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="direccion">Dirección</label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('direccion') is-invalid @enderror" 
                                                id="direccion" 
                                                name="direccion" 
                                                value="{{ old('direccion', $proveedor->direccion) }}" 
                                                placeholder="Ingrese la dirección"
                                            >
                                            @error('direccion')
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
                                                <option value="1" {{ old('status', $proveedor->status) == '1' ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ old('status', $proveedor->status) == '0' ? 'selected' : '' }}>Inactivo</option>
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
                                            <label for="observaciones">Observaciones</label>
                                            <textarea 
                                                class="form-control @error('observaciones') is-invalid @enderror" 
                                                id="observaciones" 
                                                name="observaciones" 
                                                rows="3" 
                                                placeholder="Ingrese observaciones sobre el proveedor (opcional)"
                                            >{{ old('observaciones', $proveedor->observaciones) }}</textarea>
                                            @error('observaciones')
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
                                                <a href="{{ route('inventario.proveedores.index') }}" class="btn btn-outline-secondary btn-sm">
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
    @vite(['resources/js/inventario/proveedores.js'])
@endsection