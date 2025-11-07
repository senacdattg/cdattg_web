import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
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

    const urlStore = document.getElementById('url-import-store')?.value;
    const urlStatusTemplate = document.getElementById('url-import-status')?.value;

    let pollingInterval = null;
    let currentImportId = null;

    const actualizarIncidencias = (issues) => {
        tablaIncidencias.innerHTML = '';

        if (!issues.length) {
            tablaIncidencias.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Sin incidencias registradas.</td></tr>';
            return;
        }

        issues.forEach((issue) => {
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

        panelProgreso.style.display = 'block';

        const total = total_rows ?? 0;
        const procesados = processed_rows ?? 0;
        const porcentaje = total > 0 ? Math.round((procesados / total) * 100) : 0;

        contadorProgreso.textContent = `${procesados} / ${total}`;
        porcentajeProgreso.textContent = `${porcentaje}%`;
        barraProgreso.style.width = `${porcentaje}%`;

        estadoImportacion.textContent = status.toUpperCase();
        estadoImportacion.className = 'badge';
        if (status === 'completed') {
            estadoImportacion.classList.add('bg-success');
        } else if (status === 'failed') {
            estadoImportacion.classList.add('bg-danger');
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
                Swal.fire({
                    icon: 'error',
                    title: 'Importación fallida',
                    text: datos.import.error_message,
                });
            } else if (status === 'completed') {
                Swal.fire({
                    icon: 'success',
                    title: 'Importación finalizada',
                    text: 'El procesamiento del archivo ha concluido.',
                    timer: 2500,
                    showConfirmButton: false,
                });
            }
        }
    };

    const detenerMonitoreo = () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    };

    const monitorearImportacion = (importId) => {
        currentImportId = importId;
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error de monitoreo',
                    text: error.message,
                });
            }
        };

        ejecutarConsulta();
        detenerMonitoreo();
        pollingInterval = setInterval(ejecutarConsulta, 3500);
    };

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!urlStore) {
            Swal.fire({ icon: 'error', title: 'Configuración incompleta', text: 'No se encontró la ruta para importar.' });
            return;
        }

        const archivoInput = document.getElementById('archivo_excel');
        if (!archivoInput?.files?.length) {
            Swal.fire({ icon: 'warning', title: 'Archivo requerido', text: 'Selecciona un archivo para continuar.' });
            return;
        }

        const formData = new FormData(form);
        const token = form.querySelector('input[name="_token"]').value;

        btnIniciar.disabled = true;
        btnIniciar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

        try {
            const respuesta = await fetch(urlStore, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!respuesta.ok) {
                const errorData = await respuesta.json().catch(() => ({ message: 'Error desconocido al cargar el archivo.' }));
                throw new Error(errorData.message || 'No se pudo iniciar la importación.');
            }

            const data = await respuesta.json();
            Swal.fire({ icon: 'info', title: 'Importación en marcha', text: 'El archivo se está procesando en segundo plano.' });

            btnRecargarIncidencias.disabled = true;
            panelProgreso.style.display = 'block';
            monitorearImportacion(data.import_id);
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error', text: error.message });
        } finally {
            btnIniciar.disabled = false;
            btnIniciar.innerHTML = '<i class="fas fa-play"></i> Iniciar importación';
        }
    });

    btnRecargarIncidencias?.addEventListener('click', () => {
        if (currentImportId) {
            monitorearImportacion(currentImportId);
        }
    });

    window.addEventListener('beforeunload', () => {
        detenerMonitoreo();
    });
});

