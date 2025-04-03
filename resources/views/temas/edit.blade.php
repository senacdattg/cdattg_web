@extends('adminlte::page')

@section('css')
    <!-- Estilos para Select2 y Dual Listbox -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/dual-listbox/css/bootstrap-duallistbox.min.css') }}">
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
            <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('tema.index') }}" title="Volver">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <!-- Card para Editar Tema -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Editar Tema</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tema.update', $tema->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name" class="col-form-label">Nombre:</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $tema->name) }}" required>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="status" class="col-form-label">Estado:</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" {{ $tema->status == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ $tema->status == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-check"></i> Actualizar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card para Asignar Parámetros -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Asignar Parámetros</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tema.updateParametrosTemas') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tema_id" value="{{ $tema->id }}">
                        <div class="form-group">
                            <label for="parametros">Seleccione los parámetros</label>
                            <select multiple="multiple" name="parametros[]" id="parametros" class="form-control"
                                style="height: 120px;">
                                @forelse ($parametros as $parametro)
                                    <option value="{{ $parametro->id }}" @if ($tema->parametros->contains($parametro->id)) selected @endif>
                                        {{ $parametro->name }}
                                    </option>
                                @empty
                                    <option value="" disabled>No hay parámetros disponibles</option>
                                @endforelse
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-check"></i> Agregar parámetros
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@include('layout.footer')
@endsection

@section('script')
    <!-- Scripts para Dual Listbox -->
    <script src="{{ asset('plugins/dual-listbox/js/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('select[name="parametros[]"]').bootstrapDualListbox();
        });
    </script>
@endsection
