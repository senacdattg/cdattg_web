/**
 * Módulo para recordar el estado de fullscreen entre navegaciones
 * Funciona con Livewire (sin recargas) y con navegación tradicional
 */

(function ($) {
    'use strict';

    const STORAGE_KEY = 'adminlte_fullscreen_state';

    /**
     * Verifica si el documento está en modo fullscreen
     */
    function isFullscreen() {
        return !!(
            document.fullscreenElement ||
            document.mozFullScreenElement ||
            document.webkitFullscreenElement ||
            document.msFullscreenElement
        );
    }

    /**
     * Activa el modo fullscreen
     * IMPORTANTE: Solo debe llamarse como respuesta a una interacción del usuario
     */
    function enterFullscreen() {
        const element = document.documentElement;
        try {
            if (element.requestFullscreen) {
                element.requestFullscreen().catch(() => {
                    // Silenciar errores de permisos - es normal si no hay interacción del usuario
                });
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        } catch (e) {
            // Silenciar errores - los navegadores bloquean fullscreen sin interacción del usuario
            // Esto es comportamiento esperado y no necesita mostrarse en consola
        }
    }

    /**
     * Guarda el estado en localStorage
     */
    function saveState() {
        try {
            localStorage.setItem(STORAGE_KEY, isFullscreen() ? 'true' : 'false');
        } catch (e) {
            console.warn('No se pudo guardar el estado de fullscreen:', e);
        }
    }

    /**
     * Restaura el fullscreen si el estado guardado lo requiere
     * IMPORTANTE: Los navegadores requieren interacción del usuario para activar fullscreen
     * Esta función debe llamarse DESPUÉS de una interacción del usuario
     */
    function attemptRestoreFullscreen() {
        const savedState = localStorage.getItem(STORAGE_KEY);
        if (savedState === 'true' && !isFullscreen()) {
            try {
                enterFullscreen();
            } catch (e) {
                // Silenciar errores - es normal si no hay interacción del usuario válida
                // El fullscreen se restaurará en la próxima interacción del usuario
            }
        }
    }

    /**
     * Inicializa el módulo
     */
    function init() {
        $(document).ready(function () {
            // Guardar estado cuando cambia el fullscreen
            const fullscreenEvents = [
                'fullscreenchange',
                'webkitfullscreenchange',
                'mozfullscreenchange',
                'MSFullscreenChange'
            ];

            fullscreenEvents.forEach(event => {
                $(document).on(event, function () {
                    saveState();
                });
            });

            // Interceptar clics en el botón fullscreen
            $(document).on('click', '[data-widget="fullscreen"]', function (e) {
                // Dejar que AdminLTE maneje el toggle normalmente
                // Solo guardamos el estado después
                setTimeout(() => {
                    saveState();
                }, 100);
            });

            // Si Livewire está disponible, escuchar sus eventos de navegación
            function setupLivewireHooks() {
                if (typeof Livewire !== 'undefined' && Livewire.hook) {
                    // NO restaurar fullscreen automáticamente después de actualizaciones de Livewire
                    // Solo guardar el estado para restaurarlo cuando el usuario interactúe
                    Livewire.hook('morph.updating', () => {
                        // Guardar estado antes de que Livewire actualice
                        saveState();
                    });

                    // Escuchar cuando Livewire completa una navegación
                    Livewire.hook('navigate', ({ path }) => {
                        // Guardar estado antes de navegar
                        saveState();
                    });

                    // NO restaurar automáticamente en navigated - esperar interacción del usuario
                    // El fullscreen se restaurará cuando el usuario haga clic, toque, etc.
                } else {
                    // Si Livewire aún no está listo, intentar de nuevo
                    setTimeout(setupLivewireHooks, 100);
                }
            }

            // Intentar configurar los hooks de Livewire
            setupLivewireHooks();

            // Para navegación tradicional: restaurar automáticamente después de recarga
            let restoreAttempted = false;
            const savedState = localStorage.getItem(STORAGE_KEY);

            // Función para restaurar fullscreen en la primera interacción del usuario
            function setupAutoRestore() {
                if (savedState === 'true' && !isFullscreen() && !restoreAttempted) {
                    // Múltiples eventos para capturar cualquier interacción del usuario
                    const restoreEvents = ['click', 'mousedown', 'touchstart', 'keydown'];

                    function tryRestore(e) {
                        if (!isFullscreen() && !restoreAttempted) {
                            // Intentar restaurar inmediatamente cuando el usuario interactúa
                            // Esto es necesario porque los navegadores requieren interacción del usuario
                            attemptRestoreFullscreen();
                            restoreAttempted = true;

                            // Remover todos los listeners después de restaurar
                            restoreEvents.forEach(event => {
                                document.removeEventListener(event, tryRestore, true);
                                $(document).off(event + '.fullscreen-restore');
                            });

                            // También remover el listener de visibilitychange
                            $(document).off('visibilitychange.fullscreen-restore');
                        }
                    }

                    // Registrar listeners para todos los eventos
                    // Usar addEventListener nativo con capture para interceptar ANTES
                    restoreEvents.forEach(event => {
                        document.addEventListener(event, tryRestore, true);
                        // También registrar con jQuery como respaldo
                        $(document).on(event + '.fullscreen-restore', tryRestore);
                    });

                    // NO intentar restaurar en visibilitychange sin interacción del usuario
                    // Los navegadores bloquean requestFullscreen sin interacción del usuario
                }
            }

            // Configurar restauración automática
            setupAutoRestore();

            // NO intentar restaurar automáticamente al cargar la página
            // Solo restaurar cuando el usuario interactúe (click, touch, etc.)

            // Guardar estado inicial
            saveState();
        });
    }

    // Inicializar cuando jQuery esté disponible
    if (typeof jQuery !== 'undefined') {
        init();
    } else {
        window.addEventListener('load', function () {
            if (typeof jQuery !== 'undefined') {
                init();
            }
        });
    }
})(jQuery);

