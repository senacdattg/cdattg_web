import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    const mostrarAlerta = (config = {}) => {
        if (Swal && typeof Swal.fire === 'function') {
            return Swal.fire(config);
        }

        const icon = config.icon ? `[${String(config.icon).toUpperCase()}] ` : '';
        const title = config.title ?? '';
        const message = config.text ?? config.html ?? '';

        if (config.showCancelButton) {
            const confirmado = window.confirm(`${icon}${title}\n\n${message}`);
            return Promise.resolve({ isConfirmed: confirmado });
        }

        window.alert(`${icon}${title}\n\n${message}`);
        return Promise.resolve({});
    };

    window.mostrarAlerta = mostrarAlerta;
    const form = document.getElementById('form-import-personas');
    const btnIniciar = document.getElementById('btn-iniciar-import');
    const panelProgreso = document.getElementById('panel-progreso');
    const contadorProgreso = document.getElementById('contador-progreso');
    const porcentajeProgreso = document.getElementById('porcentaje-progreso');
    const barraProgreso = document.getElementById('barra-progreso');
    const estadoImportacion = document.getElementById('estado-importacion');
    const contadorExitosos = document.getElementById('contador-exitosos');
    const contadorDuplicados = document.getElementById('contador-duplicados');
    const contadorFaltantes = document.getElementById('contador-faltantes');
    const tablaIncidencias = document.getElementById('tabla-incidencias');
    const btnRecargarIncidencias = document.getElementById('btn-recargar-incidencias');
    const btnDetenerImport = document.getElementById('btn-detener-import');
    const selectImportacionActiva = document.getElementById('select-importacion-activa');
    const urlDestroyTemplate = document.getElementById('url-import-destroy')?.value;

    const urlStore = document.getElementById('url-import-store')?.value;
    const urlStatusTemplate = document.getElementById('url-import-status')?.value;

    let pollingInterval = null;
    let currentImportId = null;

    const obtenerCsrfToken = () => {
        const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (metaToken) {
            return metaToken;
        }

        const inputToken = form?.querySelector('input[name="_token"]')?.value;
        return inputToken ?? '';
    };

    const inicializarProgreso = () => {
        panelProgreso?.classList.remove('d-none');
        if (contadorProgreso) contadorProgreso.textContent = '0 / 0';
        if (porcentajeProgreso) porcentajeProgreso.textContent = '0%';
        if (barraProgreso) barraProgreso.style.width = '0%';
        if (estadoImportacion) {
            estadoImportacion.textContent = 'PENDIENTE';
            estadoImportacion.className = 'badge bg-secondary';
        }
        if (contadorExitosos) contadorExitosos.textContent = '0';
        if (contadorDuplicados) contadorDuplicados.textContent = '0';
        if (contadorFaltantes) contadorFaltantes.textContent = '0';
        if (tablaIncidencias) {
            tablaIncidencias.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Sin incidencias registradas.</td></tr>';
        }
    };

    const actualizarControlesMonitoreo = (importId, status) => {
        if (selectImportacionActiva) {
            if (importId) {
                let opcion = selectImportacionActiva.querySelector(`option[value="${importId}"]`);
                if (!opcion) {
                    opcion = document.createElement('option');
                    opcion.value = importId;
                    opcion.textContent = `#${importId}`;
                    selectImportacionActiva.appendChild(opcion);
                }
                selectImportacionActiva.value = String(importId);
                selectImportacionActiva.disabled = false;
            } else {
                selectImportacionActiva.value = '';
            }
        }

        if (btnDetenerImport) {
            const enProceso = status === 'processing' || status === 'pending';
            btnDetenerImport.disabled = !(importId && enProceso);
        }
    };

    const actualizarIncidencias = (issues) => {
        if (!tablaIncidencias) return;

        const lista = Array.isArray(issues) ? issues : Object.values(issues ?? {});
        tablaIncidencias.innerHTML = '';

        if (!lista.length) {
            tablaIncidencias.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Sin incidencias registradas.</td></tr>';
            return;
        }

        lista.forEach((issue) => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${issue.row_number ?? '-'}</td>
                <td><span class="badge bg-warning text-dark">${issue.issue_type}</span></td>
                <td>${issue.numero_documento ?? '-'}</td>
                <td>${issue.email ?? '-'}</td>
                <td>${issue.celular ?? '-'}</td>
            `;
            tablaIncidencias.appendChild(fila);
        });
    };

    const actualizarProgreso = (datos) => {
        const { processed_rows, total_rows, success_count, duplicate_count, missing_contact_count, status } = datos.import;

        panelProgreso?.classList.remove('d-none');

        const total = total_rows ?? 0;
        const procesados = processed_rows ?? 0;
        const porcentaje = total > 0 ? Math.round((procesados / total) * 100) : 0;

        contadorProgreso.textContent = `${procesados} / ${total}`;
        porcentajeProgreso.textContent = `${porcentaje}%`;
        barraProgreso.style.width = `${porcentaje}%`;

        const statusLabels = {
            'pending': 'PENDIENTE',
            'processing': 'PROCESANDO...',
            'completed': 'COMPLETADO',
            'failed': 'FALLIDO'
        };

        estadoImportacion.textContent = statusLabels[status] || status.toUpperCase();
        estadoImportacion.className = 'badge';

        if (status === 'completed') {
            estadoImportacion.classList.add('bg-success');
            barraProgreso.classList.remove('progress-bar-animated');
        } else if (status === 'failed') {
            estadoImportacion.classList.add('bg-danger');
            barraProgreso.classList.remove('progress-bar-animated');
        } else if (status === 'processing') {
            estadoImportacion.classList.add('bg-info');
            barraProgreso.classList.add('progress-bar-animated');
        } else {
            estadoImportacion.classList.add('bg-secondary');
        }

        contadorExitosos.textContent = success_count ?? 0;
        contadorDuplicados.textContent = duplicate_count ?? 0;
        contadorFaltantes.textContent = missing_contact_count ?? 0;

        actualizarIncidencias(datos.issues ?? []);

        if (status === 'completed' || status === 'failed') {
            detenerMonitoreo();
            btnRecargarIncidencias.disabled = false;

            if (status === 'failed' && datos.import.error_message) {
                mostrarAlerta({
                    icon: 'error',
                    title: 'Importación fallida',
                    text: datos.import.error_message,
                });
            } else if (status === 'completed') {
                mostrarAlerta({
                    icon: 'success',
                    title: 'Importación finalizada',
                    text: 'El procesamiento del archivo ha concluido.',
                    timer: 2500,
                    showConfirmButton: false,
                });

                // Recargar la página después de la alerta para refrescar el historial
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }
        }

        actualizarControlesMonitoreo(datos.import.id, status);
    };

    const detenerMonitoreo = () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    };

    const monitorearImportacion = (importId) => {
        currentImportId = importId;
        actualizarControlesMonitoreo(importId, 'processing');
        const urlStatus = urlStatusTemplate.replace('__ID__', importId);

        const ejecutarConsulta = async () => {
            try {
                const respuesta = await fetch(urlStatus, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!respuesta.ok) {
                    throw new Error('Error consultando el estado de la importación');
                }

                const datos = await respuesta.json();
                actualizarProgreso(datos);
            } catch (error) {
                detenerMonitoreo();
                mostrarAlerta({
                    icon: 'error',
                    title: 'Error de monitoreo',
                    text: error.message,
                });
            }
        };

        ejecutarConsulta();
        detenerMonitoreo();
        pollingInterval = setInterval(ejecutarConsulta, 2000);
    };

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!urlStore) {
            mostrarAlerta({ icon: 'error', title: 'Configuración incompleta', text: 'No se encontró la ruta para importar.' });
            return;
        }

        const archivoInput = document.getElementById('archivo_excel');
        if (!archivoInput?.files?.length) {
            mostrarAlerta({ icon: 'warning', title: 'Archivo requerido', text: 'Selecciona un archivo para continuar.' });
            return;
        }

        const formData = new FormData(form);
        const tokenElement = form.querySelector('input[name="_token"]');

        if (!tokenElement) {
            mostrarAlerta({ icon: 'error', title: 'Error de seguridad', text: 'No se encontró el token CSRF. Por favor, recarga la página.' });
            return;
        }

        const token = tokenElement.value;

        if (btnIniciar) {
            btnIniciar.disabled = true;
            btnIniciar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
        }

        try {
            const respuesta = await fetch(urlStore, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            if (!respuesta.ok) {
                const errorData = await respuesta.json().catch(() => ({ message: 'Error desconocido al cargar el archivo.' }));
                throw new Error(errorData.message || 'No se pudo iniciar la importación.');
            }

            const data = await respuesta.json();

            btnRecargarIncidencias.disabled = true;
            inicializarProgreso();
            actualizarControlesMonitoreo(data.import_id, 'processing');

            mostrarAlerta({
                icon: 'info',
                title: 'Importación iniciada',
                text: 'El archivo se está procesando. El progreso se actualizará automáticamente.',
                timer: 2000,
                showConfirmButton: false
            });

            setTimeout(() => {
                monitorearImportacion(data.import_id);
            }, 500);
        } catch (error) {
            mostrarAlerta({ icon: 'error', title: 'Error', text: error.message });
        } finally {
            if (btnIniciar) {
                btnIniciar.disabled = false;
                btnIniciar.innerHTML = '<i class="fas fa-play"></i> Iniciar importación';
            }
        }
    });

    btnRecargarIncidencias?.addEventListener('click', () => {
        if (currentImportId) {
            monitorearImportacion(currentImportId);
        }
    });

    selectImportacionActiva?.addEventListener('change', () => {
        const seleccion = selectImportacionActiva.value;
        if (!seleccion) {
            return;
        }

        monitorearImportacion(seleccion);
    });

    btnDetenerImport?.addEventListener('click', async () => {
        if (!currentImportId || !urlDestroyTemplate) {
            return;
        }

        const csrfToken = obtenerCsrfToken();
        if (!csrfToken) {
            mostrarAlerta({
                icon: 'error',
                title: 'Token CSRF ausente',
                text: 'No fue posible obtener el token de seguridad. Recarga la página e intenta de nuevo.',
            });
            return;
        }

        const confirmar = await mostrarAlerta({
            icon: 'warning',
            title: '¿Detener importación?',
            text: 'Se cancelará el proceso y se eliminarán los registros asociados.',
            showCancelButton: true,
            confirmButtonText: 'Sí, detener',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
        });

        if (!confirmar.isConfirmed) {
            return;
        }

        try {
            btnDetenerImport.disabled = true;
            const respuesta = await fetch(urlDestroyTemplate.replace('__ID__', currentImportId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!respuesta.ok) {
                const errorData = await respuesta.json().catch(() => ({}));
                throw new Error(errorData.message || 'No fue posible detener la importación.');
            }

            detenerMonitoreo();
            mostrarAlerta({
                icon: 'success',
                title: 'Importación detenida',
                text: 'Se eliminó la importación y su historial.',
                timer: 2000,
                showConfirmButton: false,
            });

            setTimeout(() => {
                window.location.reload();
            }, 1200);
        } catch (error) {
            mostrarAlerta({
                icon: 'error',
                title: 'Error',
                text: error.message,
            });
            btnDetenerImport.disabled = false;
        }
    });

    document.querySelectorAll('.form-eliminar-import').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const result = await mostrarAlerta({
                icon: 'warning',
                title: '¿Eliminar importación?',
                text: 'Se eliminará el registro y el archivo cargado como evidencia.',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
            });

            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    document.querySelector('.custom-file-input')?.addEventListener('change', (event) => {
        const input = event.target;
        const label = input.nextElementSibling;

        if (label) {
            label.textContent = input.files?.length ? input.files[0].name : 'Elegir archivo...';
        }
    });

    window.addEventListener('beforeunload', () => {
        detenerMonitoreo();
    });
});

