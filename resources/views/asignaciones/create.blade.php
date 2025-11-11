@extends('adminlte::page')

@section('title', 'Asignar instructor a ficha')

@section('content_header')
    <h1 class="h4 mb-0">
        <i class="fas fa-user-plus mr-2 text-primary"></i>
        Nueva asignación de instructor
    </h1>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('asignaciones.instructores.store') }}" id="form-asignacion">
                            @csrf

                            <div class="form-group">
                                <label for="ficha_id" class="font-weight-bold">Ficha de caracterización</label>
                                <select
                                    name="ficha_id"
                                    id="ficha_id"
                                    class="form-control @error('ficha_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">Seleccione una ficha</option>
                                    @foreach ($fichas as $ficha)
                                        <option value="{{ $ficha->id }}" {{ old('ficha_id') == $ficha->id ? 'selected' : '' }}>
                                            {{ $ficha->ficha }} — {{ $ficha->programaFormacion->nombre ?? 'Sin programa' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ficha_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="instructor_id" class="font-weight-bold">Instructor</label>
                                <select
                                    name="instructor_id"
                                    id="instructor_id"
                                    class="form-control @error('instructor_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">Seleccione un instructor</option>
                                    @foreach ($instructores as $instructor)
                                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->nombre_completo_cache ?? $instructor->nombre_completo ?? 'Instructor sin nombre' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('instructor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="competencia_id" class="font-weight-bold">Competencia</label>
                                <select
                                    name="competencia_id"
                                    id="competencia_id"
                                    class="form-control @error('competencia_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">Seleccione una competencia</option>
                                </select>
                                @error('competencia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold d-block mb-2">Resultados de aprendizaje</label>
                                <div id="contenedor-resultados" class="border rounded p-3" style="min-height: 120px;">
                                    <p class="text-muted mb-0" id="texto-resultados-vacio">
                                        Seleccione primero una competencia para cargar sus resultados de aprendizaje.
                                    </p>
                                </div>
                                @error('resultados')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="alerta-asignacion" class="alert d-none"></div>

                            <div class="text-right">
                                <a href="{{ route('asignaciones.instructores.index') }}" class="btn btn-outline-secondary mr-2">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>
                                    Guardar asignación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <strong>Instrucciones</strong>
                    </div>
                    <div class="card-body small text-muted">
                        <ol class="pl-3 mb-0">
                            <li>Seleccione la ficha de caracterización.</li>
                            <li>Elija el instructor responsable.</li>
                            <li>Seleccione la competencia asociada a la ficha.</li>
                            <li>Marque los resultados de aprendizaje que desarrollará el instructor.</li>
                        </ol>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <strong>Resumen</strong>
                    </div>
                    <div class="card-body" id="resumen-asignacion">
                        <p class="mb-2">
                            <strong>Ficha:</strong>
                            <span class="text-muted d-block" id="resumen-ficha">—</span>
                        </p>
                        <p class="mb-2">
                            <strong>Competencia:</strong>
                            <span class="text-muted d-block" id="resumen-competencia">—</span>
                        </p>
                        <p class="mb-0">
                            <strong>Resultados seleccionados:</strong>
                            <span class="text-muted d-block" id="resumen-resultados">0 resultado(s)</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const resultadosInicialesSeleccionados = new Set(@json(old('resultados', [])));
        const competenciaSeleccionadaInicial = '{{ old('competencia_id') }}';

        document.addEventListener('DOMContentLoaded', () => {
            const fichaSelect = document.getElementById('ficha_id');
            const instructorSelect = document.getElementById('instructor_id');
            const competenciaSelect = document.getElementById('competencia_id');
            const contenedorResultados = document.getElementById('contenedor-resultados');
            const alertaAsignacion = document.getElementById('alerta-asignacion');
            const textoResultadosVacio = document.getElementById('texto-resultados-vacio');
            const resumenFicha = document.getElementById('resumen-ficha');
            const resumenCompetencia = document.getElementById('resumen-competencia');
            const resumenResultados = document.getElementById('resumen-resultados');

            const limpiarCompetencias = () => {
                competenciaSelect.innerHTML = '<option value=\"\">Seleccione una competencia</option>';
                competenciaSelect.value = '';
                resumenCompetencia.textContent = '—';
            };

            const limpiarResultados = () => {
                contenedorResultados.innerHTML = '';
                const alerta = document.createElement('p');
                alerta.classList.add('text-muted', 'mb-0');
                alerta.textContent = 'Seleccione una competencia para cargar sus resultados de aprendizaje.';
                contenedorResultados.appendChild(alerta);
                actualizarResumenResultados();
            };

            const mostrarAlerta = (mensaje, tipo = 'warning') => {
                alertaAsignacion.textContent = mensaje;
                alertaAsignacion.classList.remove('d-none', 'alert-warning', 'alert-danger', 'alert-success');
                alertaAsignacion.classList.add('alert', `alert-${tipo}`);
            };

            const ocultarAlerta = () => {
                alertaAsignacion.textContent = '';
                alertaAsignacion.classList.add('d-none');
                alertaAsignacion.classList.remove('alert-warning', 'alert-danger', 'alert-success');
            };

            const actualizarResumenResultados = () => {
                const seleccionados = contenedorResultados.querySelectorAll('input[type=\"checkbox\"]:checked').length;
                resumenResultados.textContent = `${seleccionados} resultado(s)`;
            };

            const construirCheckboxResultado = (resultado) => {
                const id = `resultado_${resultado.id}`;
                const wrapper = document.createElement('div');
                wrapper.classList.add('custom-control', 'custom-checkbox', 'mb-2');

                const input = document.createElement('input');
                input.type = 'checkbox';
                input.classList.add('custom-control-input');
                input.id = id;
                input.name = 'resultados[]';
                input.value = resultado.id;

                const label = document.createElement('label');
                label.classList.add('custom-control-label', 'd-flex', 'justify-content-between', 'w-100');
                label.setAttribute('for', id);
                label.innerHTML = `
                    <span>
                        <span class=\"badge badge-secondary mr-2\">${resultado.codigo ?? '—'}</span>
                        ${resultado.nombre}
                    </span>
                    <small class=\"text-muted\">${resultado.duracion ?? 0} h</small>
                `;

        input.addEventListener('change', () => {
            ocultarAlerta();
            actualizarResumenResultados();
        });

        wrapper.appendChild(input);
        wrapper.appendChild(label);

        return wrapper;
    };

            const cargarCompetencias = async (fichaId, seleccionar = null) => {
                limpiarCompetencias();
                limpiarResultados();

                if (!fichaId) {
                    return;
                }

                try {
                    const respuesta = await fetch(`{{ url('asignaciones/instructores/fichas') }}/${fichaId}/competencias`);
                    if (!respuesta.ok) {
                        throw new Error('No se pudo cargar la información.');
                    }

                    const json = await respuesta.json();
                    const competencias = json.data ?? [];

                    if (!competencias.length) {
                        mostrarAlerta('La ficha seleccionada no tiene competencias asociadas.', 'warning');
                        return;
                    }

                    competencias.forEach((competencia) => {
                        const option = document.createElement('option');
                        option.value = competencia.id;
                        option.textContent = `${competencia.codigo ?? ''} — ${competencia.nombre}`;
                        if (seleccionar && String(seleccionar) === String(competencia.id)) {
                            option.selected = true;
                        }
                        competenciaSelect.appendChild(option);
                    });

                    ocultarAlerta();

                    if (competenciaSelect.value) {
                        await cargarResultados(competenciaSelect.value);
                    }
                } catch (error) {
                    mostrarAlerta('Ocurrió un error al cargar las competencias.', 'danger');
                    console.error(error);
                }
            };

            const cargarResultados = async (competenciaId) => {
                contenedorResultados.innerHTML = '';

                if (!competenciaId) {
                    limpiarResultados();
                    return;
                }

                const loader = document.createElement('p');
                loader.classList.add('text-muted', 'mb-0');
                loader.textContent = 'Cargando resultados de aprendizaje...';
                contenedorResultados.appendChild(loader);

                try {
                    const respuesta = await fetch(`{{ url('asignaciones/instructores/competencias') }}/${competenciaId}/resultados`);
                    if (!respuesta.ok) {
                        throw new Error('No se pudo cargar la información.');
                    }

                    const json = await respuesta.json();
                    const resultados = json.data ?? [];

                    contenedorResultados.innerHTML = '';

                    if (!resultados.length) {
                        const alerta = document.createElement('p');
                        alerta.classList.add('text-muted', 'mb-0');
                        alerta.textContent = 'La competencia seleccionada no tiene resultados configurados.';
                        contenedorResultados.appendChild(alerta);
                        actualizarResumenResultados();
                        return;
                    }

                    resultados.forEach((resultado) => {
                        const elemento = construirCheckboxResultado(resultado);
                        const input = elemento.querySelector('input[type=\"checkbox\"]');
                        if (resultadosInicialesSeleccionados.has(String(resultado.id)) || resultadosInicialesSeleccionados.has(Number(resultado.id))) {
                            input.checked = true;
                        }
                        contenedorResultados.appendChild(elemento);
                    });

                    actualizarResumenResultados();
                    ocultarAlerta();
                } catch (error) {
                    contenedorResultados.innerHTML = '';
                    mostrarAlerta('Ocurrió un error al cargar los resultados de aprendizaje.', 'danger');
                    console.error(error);
                }
            };

            fichaSelect.addEventListener('change', async () => {
        const fichaId = fichaSelect.value;
        resumenFicha.textContent = fichaSelect.options[fichaSelect.selectedIndex]?.text || '—';
                resultadosInicialesSeleccionados.clear();
                await cargarCompetencias(fichaId);
    });

    competenciaSelect.addEventListener('change', async () => {
        const competenciaId = competenciaSelect.value;
        resumenCompetencia.textContent = competenciaSelect.options[competenciaSelect.selectedIndex]?.text || '—';
        await cargarResultados(competenciaId);
    });

    document.getElementById('form-asignacion').addEventListener('submit', (evento) => {
        const seleccionados = contenedorResultados.querySelectorAll('input[type=\"checkbox\"]:checked').length;
        if (!seleccionados) {
            evento.preventDefault();
            mostrarAlerta('Debe seleccionar al menos un resultado de aprendizaje.', 'warning');
        }
    });

    // Inicializar si hay datos previos
    if (fichaSelect.value) {
        cargarCompetencias(fichaSelect.value, competenciaSeleccionadaInicial);
    }

    instructorSelect.dispatchEvent(new Event('change'));
});
    </script>
@endsection

