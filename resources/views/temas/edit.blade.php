@extends('adminlte::page')

@section('css')
@vite('resources/css/style.css')
<style>
    #parametros_disponibles option:checked,
    #parametros_asignados option:checked {
        background: #007bff !important;
        color: #ffffff !important;
        text-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
    }

    #parametros_disponibles option:hover,
    #parametros_asignados option:hover {
        background-color: #e8f5e9 !important;
        cursor: pointer;
    }

    #parametros_disponibles,
    #parametros_asignados {
        border: 1px solid #e2e8f0;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<!-- Encabezado de la página -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $tema->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('verificarLogin') }}">Inicio</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('tema.index') }}">Temas</a>
                    </li>
                    <li class="breadcrumb-item active">Editar</li>
                    <li class="breadcrumb-item active">{{ $tema->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Contenido principal -->
<section class="content">
    <div class="container-fluid">
        <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('tema.index') }}">
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
<script>
    // Double-click handlers for both select boxes
    document.getElementById('parametros_disponibles').addEventListener('dblclick', function() {
        moveSelectedOptions('parametros_disponibles', 'parametros_asignados');
    });

    document.getElementById('parametros_asignados').addEventListener('dblclick', function() {
        moveSelectedOptions('parametros_asignados', 'parametros_disponibles');
    });

    // Button click handlers
    document.getElementById('agregar_parametro').addEventListener('click', function() {
        moveSelectedOptions('parametros_disponibles', 'parametros_asignados');
    });

    document.getElementById('quitar_parametro').addEventListener('click', function() {
        moveSelectedOptions('parametros_asignados', 'parametros_disponibles');
    });

    // // Add click handler to deselect when clicking outside
    // document.addEventListener('click', function(e) {
    //     const parametrosDisponibles = document.getElementById('parametros_disponibles');
    //     const parametrosAsignados = document.getElementById('parametros_asignados');
        
    //     if (!parametrosDisponibles.contains(e.target) && !parametrosAsignados.contains(e.target)) {
    //         parametrosDisponibles.querySelectorAll('option:checked').forEach(option => {
    //             option.selected = false;
    //         });
    //         parametrosAsignados.querySelectorAll('option:checked').forEach(option => {
    //             option.selected = false;
    //         });
    //     }
    // });

    function moveSelectedOptions(fromSelectId, toSelectId) {
        const fromSelect = document.getElementById(fromSelectId);
        const toSelect = document.getElementById(toSelectId);
        
        // If nothing is selected, select the clicked item
        if (fromSelect.selectedOptions.length === 0 && fromSelect.options.length > 0) {
            fromSelect.options[fromSelect.options.length - 1].selected = true;
        }

        // Move all selected options
        for(let option of [...fromSelect.selectedOptions]) {
            toSelect.appendChild(option);
        }

        // Sort options alphabetically
        sortSelect(toSelect);
    }

    function sortSelect(select) {
        let options = [...select.options];
        options.sort((a, b) => a.text.localeCompare(b.text));
        options.forEach(option => select.appendChild(option));
    }
</script>
@endsection