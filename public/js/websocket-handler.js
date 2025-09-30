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
            const time = new Date(`2000-01-01T${timeString}`);
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
}

// Inicializar el manejador de WebSocket cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.webSocketHandler = new WebSocketHandler();
});

// Exportar para uso global
window.WebSocketHandler = WebSocketHandler;
