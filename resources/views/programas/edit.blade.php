@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        .competencias-scroll {
            max-height: 240px;
            overflow-y: auto;
        }
    </style>
@endsection
{{-- EOF --}}





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

                                @php
                                    $competenciasAsignadas = $programa->competencias ?? collect();
                                    $valorHorasTotales = old('horas_totales', $programa->horas_totales);
                                    $valorHorasLectiva = old('horas_etapa_lectiva', $programa->horas_etapa_lectiva);
                                    $valorHorasProductiva = old(
                                        'horas_etapa_productiva',
                                        $programa->horas_etapa_productiva
                                    );
                                    $estadoSeleccionado = (int) old('status', $programa->status);
                                @endphp

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

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="horas_totales" class="form-label font-weight-bold">
                                            Horas totales del programa
                                        </label>
                                        <input
                                            type="number"
                                            name="horas_totales"
                                            id="horas_totales"
                                            class="form-control @error('horas_totales') is-invalid @enderror"
                                            value="{{ $valorHorasTotales }}"
                                            min="1"
                                            required
                                        >
                                        @error('horas_totales')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="horas_etapa_lectiva" class="form-label font-weight-bold">
                                            Horas etapa lectiva
                                        </label>
                                        <input
                                            type="number"
                                            name="horas_etapa_lectiva"
                                            id="horas_etapa_lectiva"
                                            class="form-control @error('horas_etapa_lectiva') is-invalid @enderror"
                                            value="{{ $valorHorasLectiva }}"
                                            min="1"
                                            required
                                        >
                                        @error('horas_etapa_lectiva')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="horas_etapa_productiva" class="form-label font-weight-bold">
                                            Horas etapa productiva
                                        </label>
                                        <input
                                            type="number"
                                            name="horas_etapa_productiva"
                                            id="horas_etapa_productiva"
                                            class="form-control @error('horas_etapa_productiva') is-invalid @enderror"
                                            value="{{ $valorHorasProductiva }}"
                                            min="1"
                                            required
                                        >
                                        @error('horas_etapa_productiva')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label font-weight-bold">Estado</label>
                                        <select
                                            name="status"
                                            id="status"
                                            class="form-control @error('status') is-invalid @enderror"
                                            required
                                        >
                                            <option value="1" {{ $estadoSeleccionado === 1 ? 'selected' : '' }}>
                                                Activo
                                            </option>
                                            <option value="0" {{ $estadoSeleccionado === 0 ? 'selected' : '' }}>
                                                Inactivo
                                            </option>
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

                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Competencias asociadas
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($competenciasAsignadas->isEmpty())
                                <p class="text-muted mb-0">
                                    Este programa no tiene competencias asignadas.
                                </p>
                            @else
                                <div class="table-responsive competencias-scroll">
                                    <table class="table table-sm table-striped mb-0">
                                        <caption class="sr-only">
                                            Competencias asociadas al programa
                                        </caption>
                                        <thead>
                                            <tr>
                                                <th style="width: 20%;">Código</th>
                                                <th>Nombre</th>
                                                <th style="width: 15%;" class="text-center">
                                                    Asociada a
                                                </th>
                                                <th style="width: 15%;" class="text-right">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($competenciasAsignadas as $competencia)
                                                @php
                                                    $rutaDesasociar = route(
                                                        'programa.competencia.detach',
                                                        [$programa->id, $competencia->id]
                                                    );
                                                    $mensajeConfirmacion = '¿Quitar esta competencia del programa?';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            {{ $competencia->codigo }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $competencia->nombre }}</td>
                                                    <td class="text-center">
                                                        {{ $competencia->programas_formacion_count }}
                                                    </td>
                                                    <td class="text-right">
                                                        <form
                                                            method="POST"
                                                            action="{{ $rutaDesasociar }}"
                                                            class="d-inline"
                                                        >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                data-confirm="{{ $mensajeConfirmacion }}"
                                                                onclick="return confirmarQuitar(this.dataset.confirm);"
                                                            >
                                                                <i class="fas fa-times mr-1"></i>Quitar
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <small class="text-muted d-block mt-3">
                                    Para asociar nuevas competencias utilice el módulo correspondiente.
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
<script>
    if (typeof window.confirmarQuitar !== 'function') {
        window.confirmarQuitar = function (mensaje) {
            return window.confirm(mensaje);
        };
    }
</script>
@endpush
