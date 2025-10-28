@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-graduation-cap" 
        title="Programa de Formación"
        subtitle="Edición del programa de formación"
        :breadcrumb="[['label' => 'Programas de Formación', 'url' => route('programa.index') , 'icon' => 'fa-graduation-cap'], ['label' => 'Editar programa', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('programa.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Programa de Formación
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('programa.update', $programa->id) }}" class="row">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="codigo" class="form-label font-weight-bold">Código del Programa</label>
                                        <input type="text" name="codigo" id="codigo"
                                            class="form-control @error('codigo') is-invalid @enderror"
                                            value="{{ old('codigo', $programa->codigo) }}" maxlength="6" required>
                                        @error('codigo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="form-label font-weight-bold">Nombre del Programa</label>
                                        <input type="text" name="nombre" id="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            value="{{ old('nombre', $programa->nombre) }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="red_conocimiento_id" class="form-label font-weight-bold">Red de Conocimiento</label>
                                        <select name="red_conocimiento_id" id="red_conocimiento_id" class="form-control @error('red_conocimiento_id') is-invalid @enderror" required>
                                            <option value="">Seleccione una red de conocimiento</option>
                                            @foreach(\App\Models\RedConocimiento::all() as $red)
                                                <option value="{{ $red->id }}" {{ old('red_conocimiento_id', $programa->red_conocimiento_id) == $red->id ? 'selected' : '' }}>
                                                    {{ $red->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('red_conocimiento_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nivel_formacion_id" class="form-label font-weight-bold">Nivel de Formación</label>
                                        <select name="nivel_formacion_id" id="nivel_formacion_id" class="form-control @error('nivel_formacion_id') is-invalid @enderror" required>
                                            <option value="">Seleccione un nivel de formación</option>
                                            @foreach(\App\Models\Parametro::whereIn('name', ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'])->get() as $nivel)
                                                <option value="{{ $nivel->id }}" {{ old('nivel_formacion_id', $programa->nivel_formacion_id) == $nivel->id ? 'selected' : '' }}>
                                                    {{ $nivel->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('nivel_formacion_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label font-weight-bold">Estado</label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="1" {{ old('status', $programa->status) == 1 ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ old('status', $programa->status) == 0 ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Actualizar Programa
                                        </button>
                                        <a href="{{ route('programa.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times mr-1"></i>Cancelar
                                        </a>
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