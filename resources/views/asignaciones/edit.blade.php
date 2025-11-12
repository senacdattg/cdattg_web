@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
    >
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            border: 1px solid #ced4da;
            min-height: calc(2.25rem + 2px);
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
        }

        .select2-container--bootstrap4
        .select2-selection--single
        .select2-selection__rendered {
            color: #495057;
            padding-left: 0;
        }

        .select2-container--bootstrap4
        .select2-selection--single
        .select2-selection__arrow {
            height: 100%;
            right: 0.75rem;
        }

        .select2-container--bootstrap4 .select2-dropdown {
            border-color: #ced4da;
            border-radius: 0.5rem;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }
    </style>
@endsection

@section('content_header')
    <x-page-header
        icon="fa-edit"
        title="Asignaciones de instructores"
        subtitle="Editar asignación"
        :breadcrumb="[
            [
                'label' => 'Asignaciones',
                'url' => route('asignaciones.instructores.index'),
                'icon' => 'fa-user-check',
            ],
            [
                'label' => 'Detalle',
                'url' => route('asignaciones.instructores.show', $asignacion),
                'icon' => 'fa-eye',
            ],
            [
                'label' => 'Editar',
                'icon' => 'fa-edit',
                'active' => true,
            ],
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mb-3">
                    <a
                        class="btn btn-outline-secondary btn-sm"
                        href="{{ route('asignaciones.instructores.show', $asignacion) }}"
                    >
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver al detalle
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <x-session-alerts />

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-pencil-alt mr-2"></i>
                                Editar asignación
                            </h5>
                        </div>
                        <div class="card-body">
                            <form
                                method="POST"
                                action="{{ route('asignaciones.instructores.update', $asignacion) }}"
                                id="form-asignacion"
                                class="row"
                            >
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="ficha_id"
                                            class="form-label font-weight-bold"
                                        >
                                            Ficha de caracterización
                                        </label>
                                        <select
                                            name="ficha_id"
                                            id="ficha_id"
                                            class="form-control select2-assign @error('ficha_id') is-invalid @enderror"
                                            data-placeholder="Seleccione una ficha"
                                            required
                                        >
                                            <option value="">Seleccione una ficha</option>
                                            @foreach ($fichas as $ficha)
                                                <option
                                                    value="{{ $ficha->id }}"
                                                    {{ old('ficha_id', $asignacion->ficha_id) == $ficha->id ? 'selected' : '' }}
                                                >
                                                    {{ $ficha->ficha }} —
                                                    {{ $ficha->programaFormacion->nombre ?? 'Sin programa' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ficha_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="instructor_id"
                                            class="form-label font-weight-bold"
                                        >
                                            Instructor
                                        </label>
                                        <select
                                            name="instructor_id"
                                            id="instructor_id"
                                            class="form-control select2-assign @error('instructor_id') is-invalid @enderror"
                                            data-placeholder="Seleccione un instructor"
                                            required
                                        >
                                            <option value="">Seleccione un instructor</option>
                                            @foreach ($instructores as $instructor)
                                                <option
                                                    value="{{ $instructor->id }}"
                                                    {{ old('instructor_id', $asignacion->instructor_id) == $instructor->id ? 'selected' : '' }}
                                                >
                                                    {{ $instructor->nombre_completo_cache
                                                        ?? $instructor->nombre_completo
                                                        ?? 'Instructor sin nombre' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('instructor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="competencia_id"
                                            class="form-label font-weight-bold"
                                        >
                                            Competencia
                                        </label>
                                        <select
                                            name="competencia_id"
                                            id="competencia_id"
                                            class="form-control select2-assign @error('competencia_id') is-invalid @enderror"
                                            data-placeholder="Seleccione una competencia"
                                            required
                                        >
                                            <option value="">Seleccione una competencia</option>
                                        </select>
                                        @error('competencia_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold d-block mb-2">
                                            Resultados de aprendizaje
                                        </label>
                                        <div
                                            id="contenedor-resultados"
                                            class="border rounded p-3"
                                            style="min-height: 140px;"
                                        >
                                            <p class="text-muted mb-0" id="texto-resultados-vacio">
                                                Seleccione una competencia para cargar sus resultados de aprendizaje.
                                            </p>
                                        </div>
                                        @error('resultados')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div
                                        id="alerta-asignacion"
                                        class="alert alert-warning d-none mt-3 mb-0"
                                    ></div>
                                </div>

                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a
                                            href="{{ route('asignaciones.instructores.show', $asignacion) }}"
                                            class="btn btn-light mr-2"
                                        >
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>
                                            Guardar cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <x-cards.info
                        title="Consejos de edición"
                        icon="fas fa-lightbulb"
                        color="warning"
                    >
                        <x-cards.info-item
                            label="Considere"
                            value="Verifique que la competencia pertenezca al programa de la ficha"
                            size="col-12"
                        />
                        <x-cards.info-item
                            label="Recuerde"
                            value="Un instructor puede estar asignado a varias competencias"
                            size="col-12"
                        />
                        <x-cards.info-item
                            label="Tip"
                            value="Asegúrese de seleccionar los resultados de aprendizaje correctos"
                            size="col-12"
                        />
                    </x-cards.info>

                    <div class="card detail-card no-hover mt-3" id="resumen-card">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-clipboard-list mr-2"></i>
                                Resumen de la asignación
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Ficha seleccionada:</strong><br>
                                <span class="text-muted" id="resumen-ficha">—</span>
                            </p>
                            <p class="mb-2">
                                <strong>Competencia:</strong><br>
                                <span class="text-muted" id="resumen-competencia">—</span>
                            </p>
                            <p class="mb-0">
                                <strong>Resultados seleccionados:</strong><br>
                                <span class="text-muted" id="resumen-resultados">0 resultado(s)</span>
                            </p>
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

@php
    $initialFicha = old('ficha_id', $asignacion->ficha_id);
    $initialInstructor = old('instructor_id', $asignacion->instructor_id);
    $initialCompetencia = old('competencia_id', $asignacion->competencia_id);
    $initialResultados = old(
        'resultados',
        $asignacion->resultadosAprendizaje->pluck('id')->map(fn ($id) => (string) $id)->all()
    );
@endphp

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @include(
        'asignaciones.partials.form-script',
        [
            'initialFicha' => $initialFicha,
            'initialInstructor' => $initialInstructor,
            'initialCompetencia' => $initialCompetencia,
            'initialResultados' => $initialResultados,
        ]
    )
@endsection

