/**
 * Manejador de WebSockets para notificaciones en tiempo real
 * Maneja eventos de escaneo de QR y creación de asistencias
 */

class WebSocketHandler {
    constructor() {
        this.echo = null;
        this.initialized = false;
        this.init();
    }

    init() {
        // Verificar si Echo está disponible (Laravel Echo)
        if (typeof Echo !== 'undefined') {
            this.echo = Echo;
            this.setupChannels();
            this.initialized = true;
        } else {
            console.warn('Laravel Echo no está disponible. Las notificaciones en tiempo real no funcionarán.');
        }
    }

    setupChannels() {
        // Canal para escaneos de QR
        this.echo.channel('qr-scans')
            .listen('QrScanned', (event) => {
                this.handleQrScanned(event);
            });

        // Canal para asistencias
        this.echo.channel('asistencias')
            .listen('NuevaAsistenciaRegistrada', (event) => {
                this.handleNuevaAsistenciaRegistrada(event);
            });

        // Canal para entradas/salidas de talento humano
        this.echo.channel('entradas-salidas')
            .listen('EntradaSalidaRegistrada', (event) => {
                this.handleEntradaSalidaRegistrada(event);
            });

        console.log('WebSocket channels configurados correctamente');
    }

    handleQrScanned(event) {
        console.log('QR escaneado:', event);

        const data = event.data;
        const message = `QR Escaneado: ${data.aprendiz_nombre} (${data.numero_documento}) - ${data.tipo === 'entrada' ? 'Entrada' : 'Salida'}`;

        this.showNotification(message, 'success', {
            icon: 'fas fa-qrcode',
            title: 'QR Escaneado',
            subtitle: `Ficha: ${data.ficha_id}`,
            body: `${data.aprendiz_nombre} registró ${data.tipo === 'entrada' ? 'entrada' : 'salida'} a las ${this.formatTime(data.hora_ingreso)}`
        });

        // Actualizar la interfaz si estamos en la página de QR
        this.updateQrInterface(data);
    }

    handleNuevaAsistenciaRegistrada(event) {
        console.log('Nueva asistencia registrada:', event);

        const data = event;
        const aprendizNombre = data.aprendiz || 'Aprendiz';
        const message = `Nueva asistencia: ${aprendizNombre}`;

        this.showNotification(message, 'success', {
            icon: 'fas fa-user-check',
            title: 'Nueva Asistencia',
            subtitle: `Estado: ${data.estado}`,
            body: `${aprendizNombre} registró asistencia a las ${this.formatTime(data.timestamp)}`
        });

        // Actualizar la interfaz si estamos en la página de asistencias
        this.updateAsistenciaInterface(data);
    }

    handleEntradaSalidaRegistrada(event) {
        console.log('Entrada/Salida registrada:', event);

        const data = event.data;
        const tipo = data.tipo === 'entrada' ? 'Entrada' : 'Salida';
        const nombrePersona = data.nombre_completo || `Persona ID ${data.persona_id}`;
        const sedeDescripcion = data.sede_nombre || data.sede_id || 'N/A';
        const numeroDocumento = data.numero_documento || 'N/A';
        const message = `${tipo} registrada: ${nombrePersona}`;

        this.showNotification(message, data.tipo === 'entrada' ? 'success' : 'info', {
            icon: data.tipo === 'entrada' ? 'fas fa-sign-in-alt' : 'fas fa-sign-out-alt',
            title: `${tipo} Registrada`,
            subtitle: `Rol: ${data.rol} | Sede: ${sedeDescripcion}`,
            body: `${nombrePersona} (${numeroDocumento}) registró ${data.tipo} a las ${this.formatTime(data.timestamp)}`
        });

        // Actualizar la interfaz si estamos en la página de talento humano
        this.updateEntradaSalidaInterface(data);
    }

    showNotification(message, type = 'info', options = {}) {
        // Usar AdminLTE Toast si está disponible
        if (typeof $.fn.toast !== 'undefined' && typeof Toasts !== 'undefined') {
            const toastConfig = {
                position: 'topRight',
                autohide: true,
                delay: 5000,
                icon: options.icon || 'fas fa-bell',
                title: options.title || 'Notificación',
                subtitle: options.subtitle || '',
                body: options.body || message,
                class: `bg-${type}`
            };

            const toast = new Toasts(document.createElement('div'), toastConfig);
            toast.create();
        } else {
            // Fallback a alert básico
            console.log(`[${type.toUpperCase()}] ${message}`);
        }

        // También mostrar en consola para debugging
        console.log(`[${type.toUpperCase()}] ${message}`, options);
    }

    formatTime(timeString) {
        if (!timeString) return 'N/A';

        try {
            let date;

            if (typeof timeString === 'string' && timeString.includes('T')) {
                date = new Date(timeString);
            } else {
                date = new Date(`2000-01-01T${timeString}`);
            }

            if (Number.isNaN(date.getTime())) {
                return timeString;
            }

            return time.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return timeString;
        }
    }

    updateQrInterface(data) {
        // Actualizar la tabla de aprendices si estamos en la página de QR
        const row = document.querySelector(`tr[data-documento="${data.numero_documento}"]`);
        if (row) {
            const horaIngresoCell = row.querySelector('.hora-ingreso-cell');
            if (horaIngresoCell && data.tipo === 'entrada') {
                horaIngresoCell.innerHTML = `<span class="text-success">${this.formatTime(data.hora_ingreso)}</span>`;

                // Agregar efecto visual
                row.classList.add('table-success');
                setTimeout(() => {
                    row.classList.remove('table-success');
                }, 3000);
            }
        }
    }

    updateAsistenciaInterface(data) {
        // Actualizar contadores o listas de asistencia si estamos en la página correspondiente
        const asistenciaCounter = document.getElementById('asistencia-counter');
        if (asistenciaCounter) {
            const currentCount = parseInt(asistenciaCounter.textContent) || 0;
            asistenciaCounter.textContent = currentCount + 1;

            // Efecto visual
            asistenciaCounter.classList.add('text-success');
            setTimeout(() => {
                asistenciaCounter.classList.remove('text-success');
            }, 2000);
        }

        // Actualizar tabla de asistencias si existe
        const asistenciaTable = document.getElementById('asistencia-table');
        if (asistenciaTable) {
            this.addRowToAsistenciaTable(data);
        }
    }

    addRowToAsistenciaTable(data) {
        const tbody = document.querySelector('#asistencia-table tbody');
        if (!tbody) return;

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${data.aprendiz || 'Aprendiz'}</td>
            <td>${data.id || 'N/A'}</td>
            <td>${this.formatTime(data.timestamp)}</td>
            <td>-</td>
            <td><span class="badge badge-success">${data.estado || 'Registrado'}</span></td>
        `;

        // Agregar efecto de entrada
        newRow.style.opacity = '0';
        newRow.style.transform = 'translateY(-20px)';
        tbody.insertBefore(newRow, tbody.firstChild);

        // Animación de entrada
        setTimeout(() => {
            newRow.style.transition = 'all 0.3s ease';
            newRow.style.opacity = '1';
            newRow.style.transform = 'translateY(0)';
        }, 100);

        // Remover fila después de 10 segundos si es solo para mostrar la notificación
        setTimeout(() => {
            if (newRow.parentNode) {
                newRow.style.transition = 'all 0.3s ease';
                newRow.style.opacity = '0';
                newRow.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    if (newRow.parentNode) {
                        newRow.remove();
                    }
                }, 300);
            }
        }, 10000);
    }

    // Método para conectar manualmente si es necesario
    connect() {
        if (!this.initialized) {
            this.init();
        }
    }

    // Método para desconectar
    disconnect() {
        if (this.echo) {
            this.echo.disconnect();
            this.initialized = false;
        }
    }

    updateEntradaSalidaInterface(data) {
        // Actualizar contadores en la página de talento humano
        const entradaCounter = document.getElementById('entrada-counter');
        const salidaCounter = document.getElementById('salida-counter');
        const personasDentroCounter = document.getElementById('personas-dentro-counter');

        if (data.tipo === 'entrada') {
            if (entradaCounter) {
                const currentCount = parseInt(entradaCounter.textContent) || 0;
                entradaCounter.textContent = currentCount + 1;
                entradaCounter.classList.add('text-success');
                setTimeout(() => {
                    entradaCounter.classList.remove('text-success');
                }, 2000);
            }
            if (personasDentroCounter) {
                const currentCount = parseInt(personasDentroCounter.textContent) || 0;
                personasDentroCounter.textContent = currentCount + 1;
                personasDentroCounter.classList.add('text-success');
                setTimeout(() => {
                    personasDentroCounter.classList.remove('text-success');
                }, 2000);
            }
        } else if (data.tipo === 'salida') {
            if (salidaCounter) {
                const currentCount = parseInt(salidaCounter.textContent) || 0;
                salidaCounter.textContent = currentCount + 1;
                salidaCounter.classList.add('text-info');
                setTimeout(() => {
                    salidaCounter.classList.remove('text-info');
                }, 2000);
            }
            if (personasDentroCounter) {
                const currentCount = parseInt(personasDentroCounter.textContent) || 0;
                personasDentroCounter.textContent = Math.max(0, currentCount - 1);
                personasDentroCounter.classList.add('text-warning');
                setTimeout(() => {
                    personasDentroCounter.classList.remove('text-warning');
                }, 2000);
            }
        }

        // Actualizar tabla de entradas/salidas si existe
        const entradaSalidaTable = document.getElementById('entrada-salida-table');
        if (entradaSalidaTable) {
            this.addRowToEntradaSalidaTable(data);
        }
    }

    addRowToEntradaSalidaTable(data) {
        const tbody = document.querySelector('#entrada-salida-table tbody');
        if (!tbody) return;

        const newRow = document.createElement('tr');
        const nombrePersona = data.nombre_completo || `Persona ID ${data.persona_id}`;
        const sedeDescripcion = data.sede_nombre || data.sede_id || 'N/A';
        const numeroDocumento = data.numero_documento || 'N/A';
        newRow.innerHTML = `
            <td>${data.persona_id}</td>
            <td>${nombrePersona}<br><small>${numeroDocumento}</small></td>
            <td>${data.rol}</td>
            <td>${sedeDescripcion}</td>
            <td>${data.tipo === 'entrada' ? '<span class="badge badge-success">Entrada</span>' : '<span class="badge badge-info">Salida</span>'}</td>
            <td>${this.formatTime(data.timestamp)}</td>
        `;

        // Agregar efecto de entrada
        newRow.style.opacity = '0';
        newRow.style.transform = 'translateY(-20px)';
        tbody.insertBefore(newRow, tbody.firstChild);

        // Animación de entrada
        setTimeout(() => {
            newRow.style.transition = 'all 0.3s ease';
            newRow.style.opacity = '1';
            newRow.style.transform = 'translateY(0)';
        }, 100);

        // Remover fila después de 10 segundos si es solo para mostrar la notificación
        setTimeout(() => {
            if (newRow.parentNode) {
                newRow.style.transition = 'all 0.3s ease';
                newRow.style.opacity = '0';
                newRow.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    if (newRow.parentNode) {
                        newRow.remove();
                    }
                }, 300);
            }
        }, 10000);
    }
}

// Inicializar el manejador de WebSocket cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.webSocketHandler = new WebSocketHandler();
});

// Exportar para uso global
window.WebSocketHandler = WebSocketHandler;