const horarioHoy = window.horarioHoy;

function cuandoElDocumentoEsteListo(funcionAEjecutar) {
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        funcionAEjecutar();
    } else {
        document.addEventListener('DOMContentLoaded', funcionAEjecutar);
    }
}

cuandoElDocumentoEsteListo(function() {
    // Solo inicializar el escáner si hay clases programadas
    if (window.horarioHoy) {
        // Usa Html5QrcodeScanner que maneja la UI automáticamente
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-lector", {
                fps: 25,
                qrbox: 180,
                aspectRatio: 1,
                // Si tienes múltiples cámaras y quieres especificar una
                // videoConstraints: { facingMode: { exact: "environment" } }
            },
            /* verbose= */
            false
        );

        const qrFeedbackMessages = document.querySelector('.qr-feedback-messages');
        // Obtener el id de la ficha desde un data-attribute para JS puro
        const fichaId = document.querySelector('input[name="caracterizacion_id"]').value;

        function showFeedback(message, type = 'info') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
            `;
            qrFeedbackMessages.innerHTML = alertHtml;
            setTimeout(() => {
                const alertElement = qrFeedbackMessages.querySelector('.alert');
                if (alertElement) {
                    alertElement.classList.remove('show');
                    alertElement.classList.add('fade');
                    setTimeout(() => alertElement.remove(), 150);
                }
            }, 6000);
        }

        function updateLearnerRow(documento, horaIngreso, horaSalida = null) {
            const row = document.querySelector(`tr[data-documento="${documento}"]`);
            if (row) {
                const horaIngresoCell = row.querySelector('.hora-ingreso-cell');
                const horaSalidaCell = row.querySelector('.hora-salida-cell');

                if (horaIngresoCell && horaIngreso) {
                    horaIngresoCell.innerHTML = `<span class="text-success">${horaIngreso}</span>`;
                }
                if (horaSalidaCell && horaSalida) {
                    horaSalidaCell.innerHTML = `<span class="text-info">${horaSalida}</span>`;
                }
            }
        }

        // Función que se ejecuta cuando el QR es escaneado con éxito
        const onScanSuccess = (decodedText, decodedResult) => {
            // Al escanear con éxito, detenemos el escáner para evitar múltiples lecturas rápidas
            html5QrcodeScanner.pause(); // Pausa el escáner en lugar de detenerlo completamente

            let numeroIdentificacion = decodedText.trim();

            // Verificar si el texto contiene el formato con separadores |
            if (numeroIdentificacion.includes('|')) {
                const partes = numeroIdentificacion.split('|');
                // Tomar el tercer elemento (índice 2) que es el número de documento
                if (partes.length >= 3) {
                    numeroIdentificacion = partes[2].trim();
                }
            }

            fetch(window.apiVerifyDocumentRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify({
                        numero_documento: numeroIdentificacion,
                        ficha_id: fichaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'registered') {
                        showFeedback(data.message, 'success');
                        updateLearnerRow(data.aprendiz_data.numero_documento, data.hora_ingreso);
                    } else if (data.status === 'exit_registered') {
                        showFeedback(data.message, 'success');
                        updateLearnerRow(data.aprendiz_data.numero_documento, data.hora_ingreso, data.hora_salida);
                    } else if (data.status === 'already_registered' || data.status === 'attendance_complete') {
                        showFeedback(data.message, 'info');
                        updateLearnerRow(data.aprendiz_data.numero_documento, data.hora_ingreso, data.hora_salida || null);
                    } else if (data.status === 'not_found' || data.status === 'not_a_learner' || data
                        .status === 'not_in_ficha' || data.status === 'not_assigned_instructor') {
                        showFeedback(data.message, 'danger');
                    } else {
                        showFeedback(data.message || 'Error desconocido al procesar el QR.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error de comunicación con el servidor:', error);
                    showFeedback('Error de comunicación con el servidor.', 'danger');
                })
                .finally(() => {
                    // Reanudamos el escáner después de un breve retraso para permitir la visualización del feedback
                    setTimeout(() => {
                        html5QrcodeScanner.resume();
                    }, 2000);
                });
        };

        // Función que se ejecuta si hay un error en el escaneo (normalmente inofensivo)
        const onScanFailure = (error) => {
            // Puedes imprimir el error en la consola para depuración, pero no lo muestres al usuario
            // console.warn(`Error de escaneo (normalmente inofensivo): ${error}`);
        };

        // Inicia el escáner
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }
});
