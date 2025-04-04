@extends('adminlte::page')

@section('css')
@vite('resources/css/style.css')
@endsection

@section('content_header')
<section class="content-header dashboard-header py-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 d-flex align-items-center">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px;">
                    <i class="fas fa-cogs text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Parámetro</h1>
                    <p class="text-muted mb-0 font-weight-light">Edición del parámetro</p>
                </div>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('parametro.index') }}" class="link_right_header">
                                <i class="fas fa-fw fa-paint-brush"></i> Tema
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-edit"></i> Editar tema
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Contenido principal -->
<section class="content">
    <div class="container-fluid">
        <a class="btn btn-outline-secondary btn-sm mb-3 mt-4" href="{{ route('tema.index') }}">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>

        <!-- Card para Editar Tema -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm no-hover">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title m-0 font-weight-bold text-primary">
                            <i class="fas fa-edit mr-2"></i>Editar Tema
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('tema.update', $tema->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name" class="font-weight-bold">Nombre del Tema</label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $tema->name) }}"
                                            placeholder="Ingrese el nombre del tema"
                                            required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status" class="font-weight-bold">Estado</label>
                                        <select name="status" id="status"
                                            class="form-control @error('status') is-invalid @enderror"
                                            required>
                                            <option value="1" {{ $tema->status == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ $tema->status == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('tema.index') }}" class="btn btn-light mr-2">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card para Asignar Parámetros -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm no-hover">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title m-0 font-weight-bold text-primary">
                            <i class="fas fa-list-alt mr-2"></i>Gestión de Parámetros
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tema.updateParametrosTemas') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tema_id" value="{{ $tema->id }}">

                            <div class="row">
                                <!-- Parámetros Disponibles -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            <i class="fas fa-list text-success mr-1"></i>Parámetros Disponibles
                                        </label>
                                        <select multiple class="form-control" id="parametros_disponibles"
                                            style="height: 200px;">
                                            @foreach ($parametros as $parametro)
                                            @if (!$tema->parametros->contains($parametro->id))
                                            <option value="{{ $parametro->id }}">
                                                {{ $parametro->name }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-success btn-sm mt-2" id="agregar_parametro">
                                            <i class="fas fa-arrow-right mr-1"></i> Agregar Seleccionados
                                        </button>
                                    </div>
                                </div>

                                <!-- Parámetros Asignados -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            <i class="fas fa-check-circle text-primary mr-1"></i>Parámetros Asignados
                                        </label>
                                        <select name="parametros[]" multiple class="form-control"
                                            id="parametros_asignados" style="height: 200px;">
                                            @foreach ($tema->parametros as $parametro)
                                            <option value="{{ $parametro->id }}" selected>
                                                {{ $parametro->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-danger btn-sm mt-2" id="quitar_parametro">
                                            <i class="fas fa-arrow-left mr-1"></i> Quitar Seleccionados
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right mt-4">
                                <a href="{{ route('tema.index') }}" class="btn btn-light mr-2">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
@include('layout.footer')
@endsection

@section('js')
@vite(['resources/js/tema.js'])
@endsection