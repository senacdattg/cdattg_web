@extends('adminlte::page')

@section('css')
    @vite(['resources/css/competencias.css'])
    <style>
        .form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e3e6f0; }
        .form-section:last-child { border-bottom: none; }
        .form-section-title { color: #4e73df; font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600; }
        .dashboard-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .link_right_header {
            color: #4a5568;
            text-decoration: none;
        }
        .link_right_header:hover {
            color: #4299e1;
        }
        .competencias-scroll {
            max-height: 260px;
            overflow-y: auto;
        }
        .tabla-resultados-aprendizaje td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-clipboard-list" 
        title="Competencias"
        subtitle="Gestión de competencias del SENA"
        :breadcrumb="[['label' => 'Competencias', 'url' => route('competencias.index') , 'icon' => 'fa-clipboard-list'], ['label' => 'Editar', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('competencias.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Competencia
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $horasTotalesCompetencia = (int) old('duracion', $competencia->duracion ?? 0);
                                $resultadosFormulario = collect(old('resultados'))->map(function ($resultado) {
                                    return [
                                        'id' => $resultado['id'] ?? null,
                                        'codigo' => $resultado['codigo'] ?? '',
                                        'nombre' => $resultado['nombre'] ?? '',
                                        'horas' => (int) ($resultado['horas'] ?? 0),
                                    ];
                                })->values();

                                if ($resultadosFormulario->isEmpty()) {
                                    $resultadosFormulario = $competencia->resultadosAprendizaje->map(function ($resultado) {
                                        return [
                                            'id' => $resultado->id,
                                            'codigo' => $resultado->codigo ?? '',
                                            'nombre' => $resultado->nombre,
                                            'horas' => (int) ($resultado->duracion ?? 0),
                                        ];
                                    });
                                }
                            @endphp

                            <form method="POST" action="{{ route('competencias.update', $competencia) }}">
                                @csrf
                                @method('PUT')

                                {{-- Sección: Información de la competencia --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-info-circle mr-1"></i> Información de la Competencia
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="descripcion" class="form-label font-weight-bold">
                                            Norma / Unidad de competencia <span class="text-danger">*</span>
                                        </label>
                                        <textarea
                                            name="descripcion"
                                            id="descripcion"
                                            rows="3"
                                            class="form-control @error('descripcion') is-invalid @enderror"
                                            placeholder="Describa la norma o unidad de competencia"
                                            required
                                        >{{ old('descripcion', $competencia->descripcion) }}</textarea>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Máximo 1000 caracteres.</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="codigo" class="form-label font-weight-bold">
                                                    Código de norma de competencia laboral <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="codigo"
                                                    id="codigo"
                                                    class="form-control @error('codigo') is-invalid @enderror"
                                                    value="{{ old('codigo', $competencia->codigo) }}"
                                                    maxlength="50"
                                                    required
                                                >
                                                @error('codigo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="duracion" class="form-label font-weight-bold">
                                                    Duración máxima de la competencia (horas) <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    name="duracion"
                                                    id="duracion"
                                                    class="form-control @error('duracion') is-invalid @enderror"
                                                    value="{{ old('duracion', $competencia->duracion) }}"
                                                    min="1"
                                                    required
                                                >
                                                @error('duracion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="nombre" class="form-label font-weight-bold">Nombre de la competencia <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="nombre"
                                            id="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            value="{{ old('nombre', $competencia->nombre) }}"
                                            maxlength="255"
                                            required
                                        >
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Sección: Resultados de Aprendizaje --}}
                                <div class="form-section">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h3 class="h5 mb-0 text-primary">Resultados de Aprendizaje</h3>
                                        <span class="small text-muted" id="resumen-horas-resultados"></span>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        Distribuya las {{ $horasTotalesCompetencia }} hora(s) totales de la competencia entre los resultados de aprendizaje. Las horas se redistribuyen automáticamente al agregar, quitar o modificar filas.
                                    </p>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered tabla-resultados-aprendizaje" id="tabla-resultados-aprendizaje" data-horas-totales="{{ $horasTotalesCompetencia }}">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 180px;">Código</th>
                                                    <th>Nombre del resultado de aprendizaje</th>
                                                    <th style="width: 150px;" class="text-center">Horas</th>
                                                    <th style="width: 60px;" class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($resultadosFormulario as $indice => $resultado)
                                                    @php
                                                        $horasResultado = max(0, (int) ($resultado['horas'] ?? 0));
                                                    @endphp
                                                    <tr data-index="{{ $indice }}">
                                                        <td>
                                                            <input type="hidden" class="input-id" name="resultados[{{ $indice }}][id]" value="{{ $resultado['id'] ?? '' }}">
                                                            <input type="text" class="form-control form-control-sm input-codigo" name="resultados[{{ $indice }}][codigo]" value="{{ $resultado['codigo'] ?? '' }}" placeholder="Código" maxlength="50">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm input-nombre" name="resultados[{{ $indice }}][nombre]" value="{{ $resultado['nombre'] ?? '' }}" placeholder="Nombre del resultado" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div class="input-group input-group-sm justify-content-center">
                                                                <input type="number" class="form-control text-end input-horas" name="resultados[{{ $indice }}][horas]" value="{{ $horasResultado }}" min="0" step="1" data-last-value="{{ $horasResultado }}" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">h</span>
                                            </div>
                                        </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-resultado" title="Quitar resultado">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr data-index="0">
                                                        <td>
                                                            <input type="hidden" class="input-id" name="resultados[0][id]" value="">
                                                            <input type="text" class="form-control form-control-sm input-codigo" name="resultados[0][codigo]" value="" placeholder="Código" maxlength="50">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm input-nombre" name="resultados[0][nombre]" value="" placeholder="Nombre del resultado" required>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div class="input-group input-group-sm justify-content-center">
                                                                <input type="number" class="form-control text-end input-horas" name="resultados[0][horas]" value="0" min="0" step="1" data-last-value="0" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">h</span>
                                            </div>
                                        </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-resultado" title="Quitar resultado">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="resultados-alerta" class="alert alert-warning d-none mt-3 mb-0"></div>

                                    <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="agregar-resultado-btn">
                                        <i class="fas fa-plus mr-1"></i> Agregar resultado de aprendizaje
                                    </button>
                                </div>

                                <template id="fila-resultado-template">
                                    <tr data-index="">
                                        <td>
                                            <input type="hidden" class="input-id" name="" value="">
                                            <input type="text" class="form-control form-control-sm input-codigo" name="" placeholder="Código" maxlength="50">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm input-nombre" name="" placeholder="Nombre del resultado" required>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="input-group input-group-sm justify-content-center">
                                                <input type="number" class="form-control text-end input-horas" name="" value="0" min="0" step="1" data-last-value="0" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">h</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-resultado" title="Quitar resultado">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Sección: Estado --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-toggle-on mr-1"></i> Estado
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="form-label font-weight-bold">Estado</label>
                                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                                    <option value="1" {{ old('status', $competencia->status) == '1' ? 'selected' : '' }}>Activa</option>
                                                    <option value="0" {{ old('status', $competencia->status) == '0' ? 'selected' : '' }}>Inactiva</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sección: Información de Auditoría --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-history mr-1"></i> Información de Auditoría
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Creado por:</strong> {{ $competencia->userCreate->name ?? 'N/A' }}</p>
                                            <p class="mb-1"><strong>Fecha de creación:</strong> {{ $competencia->created_at ? $competencia->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>RAPs asociados:</strong> <span class="badge badge-primary">{{ $competencia->resultadosAprendizaje->count() }}</span></p>
                                            <p class="mb-1"><strong>Última edición:</strong> {{ $competencia->updated_at ? $competencia->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Botones de Acción --}}
                                <hr class="mt-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('competencias.index') }}" class="btn btn-light mr-2">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Actualizar
                                    </button>
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

@section('js')
    @vite(['resources/js/pages/competencias-form.js'])
    <script>
        if (typeof window.confirmarQuitar !== 'function') {
            window.confirmarQuitar = function (mensaje) {
                return window.confirm(mensaje);
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            const totalHorasInput = document.getElementById('duracion');
            const tablaResultados = document.getElementById('tabla-resultados-aprendizaje');
            const cuerpoTabla = tablaResultados ? tablaResultados.querySelector('tbody') : null;
            const botonAgregar = document.getElementById('agregar-resultado-btn');
            const plantillaFila = document.getElementById('fila-resultado-template');
            const alertaResultados = document.getElementById('resultados-alerta');
            const resumenHoras = document.getElementById('resumen-horas-resultados');

            if (!tablaResultados || !cuerpoTabla || !plantillaFila) {
                return;
            }

            let recalculando = false;

            const obtenerTotalHoras = () => {
                const valor = parseInt(totalHorasInput?.value ?? tablaResultados.dataset.horasTotales ?? 0, 10);
                return Number.isNaN(valor) ? 0 : Math.max(valor, 0);
            };

            const obtenerFilas = () => Array.from(cuerpoTabla.querySelectorAll('tr[data-index]'));

            const sincronizarIndices = () => {
                obtenerFilas().forEach((fila, indice) => {
                    fila.dataset.index = indice;
                    const inputId = fila.querySelector('.input-id');
                    const inputCodigo = fila.querySelector('.input-codigo');
                    const inputNombre = fila.querySelector('.input-nombre');
                    const inputHoras = fila.querySelector('.input-horas');

                    if (inputId) {
                        inputId.name = `resultados[${indice}][id]`;
                    }
                    if (inputCodigo) {
                        inputCodigo.name = `resultados[${indice}][codigo]`;
                    }
                    if (inputNombre) {
                        inputNombre.name = `resultados[${indice}][nombre]`;
                    }
                    if (inputHoras) {
                        inputHoras.name = `resultados[${indice}][horas]`;
                    }
                });
            };

            const actualizarResumenYAlerta = () => {
                const totalHoras = obtenerTotalHoras();
                const filas = obtenerFilas();
                const sumaHoras = filas.reduce((acumulado, fila) => {
                    const inputHoras = fila.querySelector('.input-horas');
                    const valor = parseInt(inputHoras?.value ?? 0, 10);
                    return acumulado + (Number.isNaN(valor) ? 0 : valor);
                }, 0);

                if (resumenHoras) {
                    resumenHoras.textContent = `Total asignado: ${sumaHoras} h / ${totalHoras} h`;
                }

                if (!alertaResultados) {
                    return;
                }

                alertaResultados.classList.add('d-none');
                alertaResultados.classList.remove('alert-danger', 'alert-warning', 'alert-info');
                alertaResultados.textContent = '';

                if (sumaHoras > totalHoras) {
                    alertaResultados.textContent = `La suma de horas (${sumaHoras}) supera las horas totales (${totalHoras}). Ajusta los valores.`;
                    alertaResultados.classList.remove('d-none');
                    alertaResultados.classList.add('alert-danger');
                } else if (sumaHoras < totalHoras && obtenerFilas().length > 0) {
                    const diferencia = totalHoras - sumaHoras;
                    alertaResultados.textContent = `Faltan ${diferencia} hora(s) por distribuir para completar las ${totalHoras} horas totales.`;
                    alertaResultados.classList.remove('d-none');
                    alertaResultados.classList.add('alert-warning');
                }
            };

            const distribuirEquitativamente = () => {
                const filas = obtenerFilas();
                const totalHoras = obtenerTotalHoras();

                if (!filas.length) {
                    return;
                }

                const base = Math.floor(totalHoras / filas.length);
                let resto = totalHoras - base * filas.length;

                filas.forEach((fila) => {
                    const inputHoras = fila.querySelector('.input-horas');
                    if (!inputHoras) {
                        return;
                    }
                    let nuevoValor = base;
                    if (resto > 0) {
                        nuevoValor += 1;
                        resto -= 1;
                    }
                    inputHoras.value = nuevoValor;
                    inputHoras.dataset.lastValue = nuevoValor;
                });
            };

            const ajustarProporcional = (inputModificado) => {
                const filas = obtenerFilas();
                const filaModificada = inputModificado.closest('tr');
                const totalHoras = obtenerTotalHoras();
                let valorModificado = parseInt(inputModificado.value ?? 0, 10);

                if (Number.isNaN(valorModificado) || valorModificado < 0) {
                    valorModificado = 0;
                }

                if (valorModificado > totalHoras) {
                    valorModificado = totalHoras;
                }

                inputModificado.value = valorModificado;
                inputModificado.dataset.lastValue = valorModificado;

                const filasRestantes = filas.filter((fila) => fila !== filaModificada);

                if (!filasRestantes.length) {
                    return;
                }

                let horasDisponibles = totalHoras - valorModificado;
                if (horasDisponibles < 0) {
                    horasDisponibles = 0;
                }

                const sumatoriaPrevias = filasRestantes.reduce((acumulado, fila) => {
                    const inputHoras = fila.querySelector('.input-horas');
                    const previo = parseInt(inputHoras?.dataset.lastValue ?? inputHoras?.value ?? 0, 10);
                    return acumulado + (Number.isNaN(previo) ? 0 : Math.max(previo, 0));
                }, 0);

                if (sumatoriaPrevias <= 0) {
                    const base = Math.floor(horasDisponibles / filasRestantes.length);
                    let resto = horasDisponibles - base * filasRestantes.length;

                    filasRestantes.forEach((fila) => {
                        const inputHoras = fila.querySelector('.input-horas');
                        if (!inputHoras) {
                            return;
                        }
                        let nuevoValor = base;
                        if (resto > 0) {
                            nuevoValor += 1;
                            resto -= 1;
                        }
                        inputHoras.value = nuevoValor;
                        inputHoras.dataset.lastValue = nuevoValor;
                    });

                    return;
                }

                let acumuladoAsignado = 0;
                const provisional = filasRestantes.map((fila) => {
                    const inputHoras = fila.querySelector('.input-horas');
                    const previo = parseInt(inputHoras?.dataset.lastValue ?? inputHoras?.value ?? 0, 10);
                    const proporcion = (Number.isNaN(previo) ? 0 : Math.max(previo, 0)) / sumatoriaPrevias;
                    const objetivo = proporcion * horasDisponibles;
                    const base = Math.floor(objetivo);

                    acumuladoAsignado += base;

                    return {
                        fila,
                        inputHoras,
                        base,
                        fraccion: objetivo - base,
                    };
                });

                let resto = Math.round(horasDisponibles - acumuladoAsignado);

                provisional.sort((a, b) => b.fraccion - a.fraccion);

                provisional.forEach((item) => {
                    let nuevoValor = item.base;
                    if (resto > 0) {
                        nuevoValor += 1;
                        resto -= 1;
                    } else if (resto < 0) {
                        nuevoValor -= 1;
                        resto += 1;
                    }
                    item.inputHoras.value = nuevoValor;
                    item.inputHoras.dataset.lastValue = nuevoValor;
                });
            };

            const recalcularHoras = ({ modo = 'igual', input = null } = {}) => {
                if (recalculando) {
                    return;
                }

                recalculando = true;

                if (modo === 'manual' && input) {
                    ajustarProporcional(input);
                } else {
                    distribuirEquitativamente();
                }

                sincronizarIndices();
                actualizarResumenYAlerta();
                recalculando = false;
            };

            const registrarEventosFila = (fila) => {
                const botonEliminar = fila.querySelector('.btn-eliminar-resultado');
                const inputHoras = fila.querySelector('.input-horas');

                if (botonEliminar) {
                    botonEliminar.addEventListener('click', () => {
                        fila.remove();
                        recalcularHoras();
                    });
                }

                if (inputHoras) {
                    inputHoras.addEventListener('input', (evento) => {
                        const valorNumerico = parseInt(evento.target.value ?? 0, 10);
                        evento.target.value = Number.isNaN(valorNumerico) ? 0 : Math.max(valorNumerico, 0);
                        evento.target.dataset.lastValue = evento.target.value;
                        recalcularHoras({ modo: 'manual', input: evento.target });
                    });
                }
            };

            const crearFila = (datos = {}) => {
                const clon = plantillaFila.content.firstElementChild.cloneNode(true);
                const inputId = clon.querySelector('.input-id');
                const inputCodigo = clon.querySelector('.input-codigo');
                const inputNombre = clon.querySelector('.input-nombre');
                const inputHoras = clon.querySelector('.input-horas');

                if (inputId) {
                    inputId.value = datos.id ?? '';
                }
                if (inputCodigo) {
                    inputCodigo.value = datos.codigo ?? '';
                }
                if (inputNombre) {
                    inputNombre.value = datos.nombre ?? '';
                }
                if (inputHoras) {
                    const horas = Math.max(0, parseInt(datos.horas ?? 0, 10) || 0);
                    inputHoras.value = horas;
                    inputHoras.dataset.lastValue = horas;
                }

                cuerpoTabla.appendChild(clon);
                registrarEventosFila(clon);
                sincronizarIndices();
                recalcularHoras();
            };

            obtenerFilas().forEach((fila) => {
                const inputHoras = fila.querySelector('.input-horas');
                if (inputHoras) {
                    const valor = parseInt(inputHoras.value ?? 0, 10);
                    inputHoras.dataset.lastValue = Number.isNaN(valor) ? 0 : Math.max(valor, 0);
                }
                registrarEventosFila(fila);
            });

            if (!obtenerFilas().length) {
                crearFila();
            } else {
                sincronizarIndices();
                actualizarResumenYAlerta();
            }

            botonAgregar?.addEventListener('click', () => {
                crearFila();
            });

            totalHorasInput?.addEventListener('input', () => {
                recalcularHoras();
            });
        });
    </script>
@endsection
