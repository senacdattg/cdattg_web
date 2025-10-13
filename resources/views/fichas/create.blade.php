@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-plus" 
        title="Crear Ficha de Caracterización"
        subtitle="Registrar nueva ficha de caracterización"
        :breadcrumb="[['label' => 'Fichas de Caracterización', 'url' => '{{ route('fichaCaracterizacion.index') }}', 'icon' => 'fa-file-alt'], ['label' => 'Crear', 'icon' => 'fa-plus', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">
                                <i class="fas fa-file-alt mr-2"></i> Información de la Ficha
                            </h6>
                        </div>

                <form action="{{ route('fichaCaracterizacion.store') }}" method="POST" id="formCreateFicha">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Número de Ficha -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ficha">
                                        <i class="fas fa-hashtag"></i> Número de Ficha <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ficha') is-invalid @enderror" 
                                           id="ficha" name="ficha" value="{{ old('ficha') }}" 
                                           placeholder="Ej: 123456" maxlength="50" required>
                                    @error('ficha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Programa de Formación -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="programa_formacion_id">
                                        <i class="fas fa-graduation-cap"></i> Programa de Formación <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('programa_formacion_id') is-invalid @enderror" 
                                            id="programa_formacion_id" name="programa_formacion_id" required>
                                        <option value="">Seleccione un programa...</option>
                                        @foreach($programas as $programa)
                                            <option value="{{ $programa->id }}" 
                                                    {{ old('programa_formacion_id') == $programa->id ? 'selected' : '' }}
                                                    data-sede="{{ $programa->sede_id }}">
                                                {{ $programa->nombre }} ({{ $programa->codigo }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('programa_formacion_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fecha de Inicio -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">
                                        <i class="fas fa-calendar-alt"></i> Fecha de Inicio <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                           id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" required>
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fecha de Fin -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin">
                                        <i class="fas fa-calendar-alt"></i> Fecha de Fin <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                           id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}" required>
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Sede -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sede_id">
                                        <i class="fas fa-building"></i> Sede
                                    </label>
                                    <select class="form-control @error('sede_id') is-invalid @enderror" 
                                            id="sede_id" name="sede_id">
                                        <option value="">Seleccione una sede...</option>
                                        @foreach($sedes ?? [] as $sede)
                                            <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>
                                                {{ $sede->sede }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sede_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Instructor -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="instructor_id">
                                        <i class="fas fa-chalkboard-teacher"></i> Instructor Principal
                                    </label>
                                    <select class="form-control @error('instructor_id') is-invalid @enderror" 
                                            id="instructor_id" name="instructor_id">
                                        <option value="">Seleccione un instructor...</option>
                                        @foreach($instructores ?? [] as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}
                                                @if($instructor->persona->segundo_nombre)
                                                    {{ $instructor->persona->segundo_nombre }}
                                                @endif
                                                @if($instructor->persona->segundo_apellido)
                                                    {{ $instructor->persona->segundo_apellido }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Modalidad de Formación -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modalidad_formacion_id">
                                        <i class="fas fa-laptop"></i> Modalidad de Formación
                                    </label>
                                    <select class="form-control @error('modalidad_formacion_id') is-invalid @enderror" 
                                            id="modalidad_formacion_id" name="modalidad_formacion_id">
                                        <option value="">Seleccione una modalidad...</option>
                                        @foreach($modalidades ?? [] as $modalidad)
                                            <option value="{{ $modalidad->id }}" {{ old('modalidad_formacion_id') == $modalidad->id ? 'selected' : '' }}>
                                                {{ $modalidad->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('modalidad_formacion_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Jornada -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jornada_id">
                                        <i class="fas fa-clock"></i> Jornada de Formación
                                    </label>
                                    <select class="form-control @error('jornada_id') is-invalid @enderror" 
                                            id="jornada_id" name="jornada_id">
                                        <option value="">Seleccione una jornada...</option>
                                        @foreach($jornadas ?? [] as $jornada)
                                            <option value="{{ $jornada->id }}" {{ old('jornada_id') == $jornada->id ? 'selected' : '' }}>
                                                {{ $jornada->jornada }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jornada_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Ambiente -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ambiente_id">
                                        <i class="fas fa-door-open"></i> Ambiente
                                    </label>
                                    <select class="form-control @error('ambiente_id') is-invalid @enderror" 
                                            id="ambiente_id" name="ambiente_id" disabled>
                                        <option value="">Primero seleccione una sede...</option>
                                    </select>
                                    @error('ambiente_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Total de Horas -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_horas">
                                        <i class="fas fa-hourglass-half"></i> Total de Horas
                                    </label>
                                    <input type="number" class="form-control @error('total_horas') is-invalid @enderror" 
                                           id="total_horas" name="total_horas" value="{{ old('total_horas') }}" 
                                           min="1" max="9999" placeholder="Ej: 120">
                                    @error('total_horas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Estado -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="status" name="status" value="1" 
                                               {{ old('status', '1') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">
                                            <i class="fas fa-toggle-on"></i> Ficha Activa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('fichaCaracterizacion.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Crear Ficha
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @vite(['resources/js/pages/fichas-form.js'])
@endsection