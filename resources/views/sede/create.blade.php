@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Sedes"
        subtitle="Gestión de sedes del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Sedes', 'url' => route('sede.index'), 'icon' => 'fa-cog'], ['label' => 'Crear Sede', 'icon' => 'fa-plus', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Botón Volver -->
            <div class="mb-3">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('sede.index') }}" title="Volver">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-building mr-2"></i>Crear Sede
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('sede.store') }}" class="row">
                                @csrf

                                <!-- Nombre de la Sede -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sede" class="form-label font-weight-bold">Nombre de la Sede</label>
                                        <input type="text" 
                                               id="sede" 
                                               name="sede" 
                                               class="form-control @error('sede') is-invalid @enderror" 
                                               value="{{ old('sede') }}" 
                                               required 
                                               autofocus
                                               placeholder="Ingrese el nombre de la sede">
                                        @error('sede')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Dirección -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion" class="form-label font-weight-bold">Dirección</label>
                                        <input type="text" 
                                               id="direccion" 
                                               name="direccion" 
                                               class="form-control @error('direccion') is-invalid @enderror" 
                                               value="{{ old('direccion') }}" 
                                               required
                                               placeholder="Ingrese la dirección">
                                        @error('direccion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Regional -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="regional_id" class="form-label font-weight-bold">Regional</label>
                                        <select name="regional_id" 
                                                id="regional_id" 
                                                class="form-control @error('regional_id') is-invalid @enderror" 
                                                required>
                                            <option value="">Seleccione una regional</option>
                                            @foreach ($regionales as $regional)
                                                <option value="{{ $regional->id }}" {{ old('regional_id') == $regional->id ? 'selected' : '' }}>
                                                    {{ $regional->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('regional_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Departamento -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="departamento_id" class="form-label font-weight-bold">Departamento</label>
                                        <select name="departamento_id" 
                                                id="departamento_id" 
                                                class="form-control @error('departamento_id') is-invalid @enderror">
                                            <option value="">Seleccione un departamento</option>
                                            @foreach($departamentos as $departamento)
                                                <option value="{{ $departamento->id }}" 
                                                    {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>
                                                    {{ $departamento->departamento }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('departamento_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Municipio -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="municipio_id" class="form-label font-weight-bold">Municipio</label>
                                        <select name="municipio_id" 
                                                id="municipio_id" 
                                                class="form-control @error('municipio_id') is-invalid @enderror" 
                                                required>
                                            <option value="">Primero seleccione un departamento</option>
                                            @foreach($municipios as $municipio)
                                                <option value="{{ $municipio->id }}" 
                                                    data-departamento="{{ $municipio->departamento_id }}"
                                                    {{ old('municipio_id') == $municipio->id ? 'selected' : '' }}>
                                                    {{ $municipio->municipio }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('municipio_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('sede.index') }}" class="btn btn-outline-secondary btn-sm mx-1">
                                            <i class="fas fa-times mr-1"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-outline-success btn-sm mx-1">
                                            <i class="fas fa-save mr-1"></i> Crear Sede
                                        </button>
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

@section('plugins.Chartjs', true)

@section('js')
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const regionalSelect = document.getElementById('regional_id');
            const departamentoSelect = document.getElementById('departamento_id');
            const municipioSelect = document.getElementById('municipio_id');
            
            // Datos de regionales con sus departamentos
            const regionalesData = {!! json_encode($regionales->map(function($r) {
                return [
                    'id' => $r->id,
                    'departamento_id' => $r->departamento_id
                ];
            })) !!};
            
            // Función para filtrar municipios
            function filtrarMunicipios() {
                const departamentoId = departamentoSelect.value;
                const municipioOptions = municipioSelect.querySelectorAll('option');
                
                municipioOptions.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                    } else if (departamentoId === '') {
                        option.style.display = 'none';
                    } else if (option.dataset.departamento === departamentoId) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                if (departamentoId === '') {
                    municipioSelect.value = '';
                    municipioSelect.disabled = true;
                } else {
                    municipioSelect.disabled = false;
                }
            }
            
            // Cuando se selecciona una regional, seleccionar su departamento automáticamente
            regionalSelect.addEventListener('change', function() {
                const regionalId = this.value;
                
                if (regionalId) {
                    // Buscar el departamento de la regional seleccionada
                    const regionalData = regionalesData.find(r => r.id == regionalId);
                    
                    if (regionalData && regionalData.departamento_id) {
                        // Seleccionar el departamento automáticamente
                        departamentoSelect.value = regionalData.departamento_id;
                        
                        // Filtrar municipios
                        filtrarMunicipios();
                    }
                } else {
                    departamentoSelect.value = '';
                    filtrarMunicipios();
                }
            });
            
            // Escuchar cambios en departamento
            departamentoSelect.addEventListener('change', filtrarMunicipios);
            
            // Ejecutar al cargar si hay valor previo
            filtrarMunicipios();
        });
    </script>
@endsection
