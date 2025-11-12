@php
    $initialResultados = collect($initialResultados ?? [])
        ->map(fn ($id) => (string) $id)
        ->values();
@endphp

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const $ = window.jQuery ?? null;
        const tieneSelect2 = !!($ && $.fn && $.fn.select2);

        if (tieneSelect2) {
            $('.select2-assign').each(function () {
                const $elemento = $(this);
                const placeholder = $elemento.data('placeholder') || 'Seleccione una opción';
                $elemento.select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder,
                    language: {
                        noResults: () => 'No se encontraron resultados',
                        searching: () => 'Buscando...',
                    },
                });
            });
        }

        const fichaSelect = document.getElementById('ficha_id');
        const instructorSelect = document.getElementById('instructor_id');
        const competenciaSelect = document.getElementById('competencia_id');
        const contenedorResultados = document.getElementById('contenedor-resultados');
        const alertaAsignacion = document.getElementById('alerta-asignacion');
        const resumenFicha = document.getElementById('resumen-ficha');
        const resumenCompetencia = document.getElementById('resumen-competencia');
        const resumenResultados = document.getElementById('resumen-resultados');

        const configuracionInicial = {
            ficha: '{{ $initialFicha ?? '' }}',
            instructor: '{{ $initialInstructor ?? '' }}',
            competencia: '{{ $initialCompetencia ?? '' }}',
            resultados: new Set(@json($initialResultados)),
        };

        let competenciaInicialPendiente = configuracionInicial.competencia
            ? String(configuracionInicial.competencia)
            : null;

        const obtenerPlaceholder = (select) =>
            select?.dataset?.placeholder || 'Seleccione una opción';

        const obtenerTextoSeleccionado = (select) => {
            if (!select) {
                return '—';
            }
            const opcion = select.options[select.selectedIndex];
            if (!opcion || opcion.value === '') {
                return '—';
            }
            return opcion.text;
        };

        const actualizarOpcionesSelect = (selectElement, opciones, seleccionado = null) => {
            if (!selectElement) {
                return;
            }

            const placeholder = obtenerPlaceholder(selectElement);

            if (tieneSelect2) {
                const $select = $(selectElement);
                $select.empty();
                $select.append(new Option(placeholder, '', true, false));
                opciones.forEach((opcion) => {
                    $select.append(new Option(opcion.texto, opcion.valor, false, false));
                });
                $select.val(seleccionado ? String(seleccionado) : '').trigger('change');
            } else {
                selectElement.innerHTML = '';
                const opcionInicial = document.createElement('option');
                opcionInicial.value = '';
                opcionInicial.textContent = placeholder;
                selectElement.appendChild(opcionInicial);

                opciones.forEach((opcion) => {
                    const opt = document.createElement('option');
                    opt.value = opcion.valor;
                    opt.textContent = opcion.texto;
                    selectElement.appendChild(opt);
                });

                selectElement.value = seleccionado ? String(seleccionado) : '';
                selectElement.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };

        const limpiarCompetencias = () => {
            if (!competenciaSelect) {
                return;
            }
            actualizarOpcionesSelect(competenciaSelect, []);
            if (!tieneSelect2 && resumenCompetencia) {
                resumenCompetencia.textContent = '—';
            }
        };

        const limpiarResultados = () => {
            if (!contenedorResultados) {
                return;
            }

            contenedorResultados.innerHTML = '';
            const mensaje = document.createElement('p');
            mensaje.classList.add('text-muted', 'mb-0');
            mensaje.textContent =
                'Seleccione una competencia para cargar sus resultados de aprendizaje.';
            contenedorResultados.appendChild(mensaje);
            actualizarResumenResultados();
        };

        const mostrarAlerta = (mensaje, tipo = 'warning') => {
            if (!alertaAsignacion) {
                return;
            }
            alertaAsignacion.textContent = mensaje;
            alertaAsignacion.classList.remove('d-none', 'alert-warning', 'alert-danger', 'alert-success');
            alertaAsignacion.classList.add('alert', `alert-${tipo}`);
        };

        const ocultarAlerta = () => {
            if (!alertaAsignacion) {
                return;
            }
            alertaAsignacion.textContent = '';
            alertaAsignacion.classList.add('d-none');
            alertaAsignacion.classList.remove('alert-warning', 'alert-danger', 'alert-success');
        };

        const actualizarResumenResultados = () => {
            if (!resumenResultados || !contenedorResultados) {
                return;
            }
            const seleccionados = contenedorResultados.querySelectorAll(
                'input[type="checkbox"]:checked'
            ).length;
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

            if (configuracionInicial.resultados.has(String(resultado.id))) {
                input.checked = true;
            }

            const label = document.createElement('label');
            label.classList.add(
                'custom-control-label',
                'd-flex',
                'justify-content-between',
                'w-100'
            );
            label.setAttribute('for', id);
            label.innerHTML = `
                <span>
                    <span class="badge badge-secondary mr-2">${resultado.codigo ?? '—'}</span>
                    ${resultado.nombre}
                </span>
                <small class="text-muted">${resultado.duracion ?? 0} h</small>
            `;

            input.addEventListener('change', () => {
                ocultarAlerta();
                actualizarResumenResultados();
            });

            wrapper.appendChild(input);
            wrapper.appendChild(label);

            return wrapper;
        };

        const cargarResultados = async (competenciaId) => {
            if (!contenedorResultados) {
                return;
            }

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
                const respuesta = await fetch(
                    `{{ url('asignaciones/instructores/competencias') }}/${competenciaId}/resultados`
                );
                if (!respuesta.ok) {
                    throw new Error('No se pudo cargar la información.');
                }

                const json = await respuesta.json();
                const resultados = json.data ?? [];

                contenedorResultados.innerHTML = '';

                if (!resultados.length) {
                    const alerta = document.createElement('p');
                    alerta.classList.add('text-muted', 'mb-0');
                    alerta.textContent =
                        'La competencia seleccionada no tiene resultados configurados.';
                    contenedorResultados.appendChild(alerta);
                    actualizarResumenResultados();
                    return;
                }

                resultados.forEach((resultado) => {
                    contenedorResultados.appendChild(construirCheckboxResultado(resultado));
                });

                actualizarResumenResultados();
                ocultarAlerta();
            } catch (error) {
                contenedorResultados.innerHTML = '';
                mostrarAlerta(
                    'Ocurrió un error al cargar los resultados de aprendizaje.',
                    'danger'
                );
                console.error(error);
            }
        };

        const cargarCompetencias = async (fichaId, seleccionar = null) => {
            if (!competenciaSelect) {
                return;
            }

            limpiarCompetencias();
            limpiarResultados();

            if (!fichaId) {
                return;
            }

            try {
                const respuesta = await fetch(
                    `{{ url('asignaciones/instructores/fichas') }}/${fichaId}/competencias`
                );
                if (!respuesta.ok) {
                    throw new Error('No se pudo cargar la información.');
                }

                const json = await respuesta.json();
                const competencias = json.data ?? [];

                if (!competencias.length) {
                    mostrarAlerta('La ficha seleccionada no tiene competencias asociadas.', 'warning');
                    return;
                }

                const opciones = competencias.map((competencia) => {
                    const partes = [competencia.codigo, competencia.nombre].filter(Boolean);
                    return {
                        valor: competencia.id,
                        texto: partes.join(' — '),
                    };
                });

                const valorSeleccionado = seleccionar ?? null;
                actualizarOpcionesSelect(competenciaSelect, opciones, valorSeleccionado);

                if (!tieneSelect2 && resumenCompetencia) {
                    resumenCompetencia.textContent = obtenerTextoSeleccionado(competenciaSelect);
                }
            } catch (error) {
                mostrarAlerta('Ocurrió un error al cargar las competencias.', 'danger');
                console.error(error);
            }
        };

        if (fichaSelect) {
            fichaSelect.addEventListener('change', async () => {
                if (resumenFicha) {
                    resumenFicha.textContent = obtenerTextoSeleccionado(fichaSelect);
                }
                configuracionInicial.resultados.clear();
                await cargarCompetencias(fichaSelect.value, competenciaInicialPendiente);
                competenciaInicialPendiente = null;
            });
        }

        if (competenciaSelect) {
            competenciaSelect.addEventListener('change', async () => {
                if (resumenCompetencia) {
                    resumenCompetencia.textContent = obtenerTextoSeleccionado(competenciaSelect);
                }
                await cargarResultados(competenciaSelect.value);
            });
        }

        const formulario = document.getElementById('form-asignacion');
        if (formulario) {
            formulario.addEventListener('submit', (evento) => {
                const seleccionados = contenedorResultados
                    ? contenedorResultados.querySelectorAll('input[type="checkbox"]:checked').length
                    : 0;

                if (seleccionados === 0) {
                    evento.preventDefault();
                    mostrarAlerta(
                        'Debe seleccionar al menos un resultado de aprendizaje.',
                        'warning'
                    );
                }
            });
        }

        if (resumenFicha && fichaSelect) {
            resumenFicha.textContent = obtenerTextoSeleccionado(fichaSelect);
        }
        if (resumenCompetencia && competenciaSelect) {
            resumenCompetencia.textContent = obtenerTextoSeleccionado(competenciaSelect);
        }

        const establecerValorInicial = (selectElement, valor) => {
            if (!selectElement || !valor) {
                return false;
            }
            if (tieneSelect2) {
                $(selectElement).val(String(valor)).trigger('change');
            } else {
                selectElement.value = String(valor);
                selectElement.dispatchEvent(new Event('change', { bubbles: true }));
            }
            return true;
        };

        const fichaInicialEstablecida = establecerValorInicial(
            fichaSelect,
            configuracionInicial.ficha
        );

        if (!fichaInicialEstablecida) {
            limpiarCompetencias();
            limpiarResultados();
        }
    });
</script>
