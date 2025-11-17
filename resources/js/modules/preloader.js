/**
 * Módulo para controlar el preloader - SOLO como respaldo
 * La lógica principal está en el script inline de preloader.blade.php
 * Este módulo solo asegura que el preloader se oculte si el script inline falla
 */

(function () {
    'use strict';

    // Esperar un momento para que el script inline haga su trabajo
    setTimeout(function () {
        var preloader = document.getElementById('sena-preloader');
        if (preloader && preloader.style.display !== 'none') {
            // Si el preloader aún está visible, ocultarlo
            preloader.style.display = 'none';
            preloader.style.opacity = '0';
            preloader.style.visibility = 'hidden';
            document.body.classList.add('preloader-ready');

            setTimeout(function () {
                if (preloader && preloader.parentNode) {
                    preloader.remove();
                }
            }, 100);
        }
    }, 2500);

})();
