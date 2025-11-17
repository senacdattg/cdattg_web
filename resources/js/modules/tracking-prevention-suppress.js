/**
 * Suprime errores de Tracking Prevention en la consola
 * Evita spam de mensajes cuando el navegador bloquea acceso a storage
 */
export function suppressTrackingPreventionErrors() {
    const originalError = console.error;
    const originalWarn = console.warn;

    console.error = function(...args) {
        const message = args[0]?.toString() || '';
        if (message.includes('Tracking Prevention blocked access to storage')) {
            return; // Silenciar estos errores
        }
        originalError.apply(console, args);
    };

    console.warn = function(...args) {
        const message = args[0]?.toString() || '';
        if (message.includes('Tracking Prevention blocked access to storage')) {
            return; // Silenciar estos warnings
        }
        originalWarn.apply(console, args);
    };

    // Interceptar errores globales
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('Tracking Prevention blocked access to storage')) {
            e.preventDefault();
            return false;
        }
    });

    // Interceptar promesas rechazadas no manejadas
    window.addEventListener('unhandledrejection', function(e) {
        if (e.reason && e.reason.toString().includes('Tracking Prevention blocked access to storage')) {
            e.preventDefault();
            return false;
        }
    });
}

