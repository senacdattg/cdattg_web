/**
 * Configuración de Livewire para excluir la ruta de logout del prefetch
 * Esto evita errores 405 cuando Livewire intenta hacer prefetch de /logout
 */
(function() {
    'use strict';

    function configureLivewirePrefetch() {
        if (typeof window.Livewire === 'undefined') {
            return;
        }

        // Interceptar el prefetch de Livewire
        if (window.Livewire.hook) {
            window.Livewire.hook('morph.prefetch', ({ path, cancel }) => {
                // Excluir la ruta de logout del prefetch
                if (path && (path.includes('/logout') || path.endsWith('logout'))) {
                    cancel();
                }
            });
        }

        // También interceptar el evento de prefetch si está disponible
        document.addEventListener('livewire:prefetch', function(event) {
            const url = event.detail?.url || event.detail?.path;
            if (url && (url.includes('/logout') || url.endsWith('logout'))) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, true);
    }

    // Intentar configurar cuando Livewire esté disponible
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', configureLivewirePrefetch);
    } else {
        // Si el DOM ya está cargado, intentar inmediatamente
        configureLivewirePrefetch();
        
        // También intentar después de un pequeño delay por si Livewire aún no está listo
        setTimeout(configureLivewirePrefetch, 100);
    }

    // Si Livewire se carga después, intentar de nuevo
    if (typeof window.Livewire === 'undefined') {
        const checkLivewire = setInterval(function() {
            if (typeof window.Livewire !== 'undefined') {
                clearInterval(checkLivewire);
                configureLivewirePrefetch();
            }
        }, 100);

        // Limpiar el intervalo después de 5 segundos
        setTimeout(function() {
            clearInterval(checkLivewire);
        }, 5000);
    }
})();

