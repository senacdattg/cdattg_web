/**
 * Módulo para asegurar la persistencia del estado del sidebar izquierdo
 * Funciona con Livewire (SPA) y navegación tradicional
 */

(function ($) {
    'use strict';

    const STORAGE_KEY = 'rememberlte.pushmenu';
    const COLLAPSED_CLASS = 'sidebar-collapse';

    /**
     * Verifica si el sidebar está colapsado
     */
    function isSidebarCollapsed() {
        return $('body').hasClass(COLLAPSED_CLASS);
    }

    /**
     * Guarda el estado del sidebar en localStorage
     */
    function saveSidebarState() {
        const isCollapsed = isSidebarCollapsed();
        const value = isCollapsed ? COLLAPSED_CLASS : '';

        // Usar StorageSafe si está disponible, sino fallback a try-catch
        if (typeof window.StorageSafe !== 'undefined') {
            window.StorageSafe.setItem(STORAGE_KEY, value);
        } else {
            try {
                localStorage.setItem(STORAGE_KEY, value);
            } catch (e) {
                // Silenciar errores de Tracking Prevention
            }
        }
    }

    /**
     * Restaura el estado del sidebar desde localStorage
     */
    function restoreSidebarState() {
        try {
            let savedState = '';

            // Usar StorageSafe si está disponible, sino fallback a try-catch
            if (typeof window.StorageSafe !== 'undefined') {
                savedState = window.StorageSafe.getItem(STORAGE_KEY, 'localStorage', '') || '';
            } else {
                try {
                    savedState = localStorage.getItem(STORAGE_KEY) || '';
                } catch (e) {
                    // Silenciar errores de Tracking Prevention
                    savedState = '';
                }
            }

            const $body = $('body');

            if (savedState === COLLAPSED_CLASS && !isSidebarCollapsed()) {
                // Restaurar estado colapsado sin transición para evitar parpadeo
                $body.addClass('hold-transition').addClass(COLLAPSED_CLASS);
                setTimeout(function () {
                    $body.removeClass('hold-transition');
                }, 50);
            } else if (savedState !== COLLAPSED_CLASS && isSidebarCollapsed()) {
                // Restaurar estado expandido sin transición
                $body.addClass('hold-transition').removeClass(COLLAPSED_CLASS);
                setTimeout(function () {
                    $body.removeClass('hold-transition');
                }, 50);
            }
        } catch (e) {
            console.warn('No se pudo restaurar el estado del sidebar:', e);
        }
    }

    /**
     * Inicializa el módulo
     */
    function init() {
        $(document).ready(function () {
            // Restaurar estado inicial
            restoreSidebarState();

            // Guardar estado cuando el sidebar cambia
            $(document).on('collapsed.lte.pushmenu', '[data-widget="pushmenu"]', function () {
                saveSidebarState();
            });

            $(document).on('shown.lte.pushmenu', '[data-widget="pushmenu"]', function () {
                saveSidebarState();
            });

            // También escuchar cambios directos en la clase del body
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (mutation.target === document.body) {
                            saveSidebarState();
                        }
                    }
                });
            });

            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Si Livewire está disponible, restaurar estado después de navegaciones
            if (typeof window.Livewire !== 'undefined') {
                // Escuchar cuando Livewire completa una navegación
                document.addEventListener('livewire:navigated', function () {
                    // Pequeño delay para asegurar que AdminLTE se haya inicializado
                    setTimeout(function () {
                        restoreSidebarState();
                    }, 100);
                });

                // También usar hooks de Livewire si están disponibles
                if (window.Livewire && window.Livewire.hook) {
                    window.Livewire.hook('navigated', function () {
                        setTimeout(function () {
                            restoreSidebarState();
                        }, 100);
                    });

                    window.Livewire.hook('morph.updated', function () {
                        setTimeout(function () {
                            restoreSidebarState();
                        }, 100);
                    });
                }
            }

            // Guardar estado inicial
            saveSidebarState();
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

