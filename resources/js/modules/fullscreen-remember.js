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
     */
    function enterFullscreen() {
        const element = document.documentElement;
        if (element.requestFullscreen) {
            element.requestFullscreen().catch(() => { });
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
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
                // Verificar si se activó correctamente después de un breve delay
                setTimeout(() => {
                    if (!isFullscreen()) {
                        // Si falló, intentar de nuevo en el próximo evento
                        console.warn('Fullscreen no se pudo activar automáticamente. Requiere interacción del usuario.');
                    }
                }, 100);
            } catch (e) {
                console.warn('Error al restaurar fullscreen:', e);
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
                    // Restaurar fullscreen después de que Livewire actualice la página
                    Livewire.hook('morph.updated', () => {
                        const savedState = localStorage.getItem(STORAGE_KEY);
                        if (savedState === 'true' && !isFullscreen()) {
                            // Esperar un momento para que el DOM se estabilice
                            setTimeout(() => {
                                attemptRestoreFullscreen();
                            }, 200);
                        }
                    });

                    // También escuchar cuando Livewire navega
                    Livewire.hook('morph.updating', () => {
                        // Guardar estado antes de que Livewire actualice
                        saveState();
                    });

                    // Escuchar cuando Livewire completa una navegación
                    Livewire.hook('navigate', ({ path }) => {
                        // Guardar estado antes de navegar
                        saveState();
                    });

                    // Escuchar cuando Livewire completa la navegación
                    Livewire.hook('navigated', () => {
                        const savedState = localStorage.getItem(STORAGE_KEY);
                        if (savedState === 'true' && !isFullscreen()) {
                            setTimeout(() => {
                                attemptRestoreFullscreen();
                            }, 300);
                        }
                    });
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

                    // También intentar restaurar cuando la página se vuelve visible
                    $(document).on('visibilitychange.fullscreen-restore', function () {
                        if (!document.hidden && !isFullscreen() && !restoreAttempted) {
                            // Esperar un momento y luego intentar restaurar
                            setTimeout(() => {
                                if (!restoreAttempted) {
                                    attemptRestoreFullscreen();
                                    restoreAttempted = true;
                                }
                            }, 100);
                        }
                    });
                }
            }

            // Configurar restauración automática
            setupAutoRestore();

            // También intentar restaurar cuando el DOM esté completamente listo
            if (savedState === 'true' && !isFullscreen()) {
                // Esperar un poco más para asegurar que todo esté cargado
                setTimeout(() => {
                    if (!restoreAttempted && !isFullscreen()) {
                        // Marcar que intentaremos restaurar en la próxima interacción
                        restoreAttempted = false;
                        setupAutoRestore();
                    }
                }, 500);
            }

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

