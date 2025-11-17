{{-- Preloader inicial - renderizado inmediato --}}
<div id="sena-preloader">
    <img src="{{ asset(config('adminlte.preloader.img.path', 'vendor/adminlte/dist/img/LogoSena.png')) }}" alt="SENA"
        width="80" height="80" loading="eager">
    <div class="sena-preloader-text">SENA {{ date('Y') }}</div>
</div>
{{-- Script inline: suprimir errores de Tracking Prevention y ocultar preloader --}}
<script>
    // Suprimir errores de Tracking Prevention globalmente - DEBE EJECUTARSE PRIMERO
    (function() {
        'use strict';
        
        // Interceptar console.error y console.warn ANTES que cualquier otra cosa
        const originalError = console.error;
        const originalWarn = console.warn;
        const originalLog = console.log;
        
        function shouldSuppress(message) {
            if (!message) return false;
            const msg = message.toString().toLowerCase();
            return msg.includes('tracking prevention') || 
                   msg.includes('blocked access to storage') ||
                   msg.includes('quotaexceedederror') ||
                   msg.includes('securityerror');
        }
        
        console.error = function(...args) {
            const message = args[0]?.toString() || '';
            if (shouldSuppress(message)) {
                return; // Silenciar completamente
            }
            originalError.apply(console, args);
        };
        
        console.warn = function(...args) {
            const message = args[0]?.toString() || '';
            if (shouldSuppress(message)) {
                return; // Silenciar completamente
            }
            originalWarn.apply(console, args);
        };
        
        // Interceptar errores no manejados
        window.addEventListener('error', function(e) {
            if (e.message && shouldSuppress(e.message)) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        }, true);
        
        // Interceptar promesas rechazadas no manejadas
        window.addEventListener('unhandledrejection', function(e) {
            if (e.reason && shouldSuppress(e.reason.toString())) {
                e.preventDefault();
                return false;
            }
        });
        
        // Crear wrappers seguros para localStorage y sessionStorage
        function createSafeStorage(originalStorage) {
            return {
                getItem: function(key) {
                    try {
                        return originalStorage.getItem(key);
                    } catch (e) {
                        return null;
                    }
                },
                setItem: function(key, value) {
                    try {
                        originalStorage.setItem(key, value);
                    } catch (e) {
                        // Silenciar errores
                    }
                },
                removeItem: function(key) {
                    try {
                        originalStorage.removeItem(key);
                    } catch (e) {
                        // Silenciar errores
                    }
                },
                clear: function() {
                    try {
                        originalStorage.clear();
                    } catch (e) {
                        // Silenciar errores
                    }
                },
                get length() {
                    try {
                        return originalStorage.length;
                    } catch (e) {
                        return 0;
                    }
                },
                key: function(index) {
                    try {
                        return originalStorage.key(index);
                    } catch (e) {
                        return null;
                    }
                }
            };
        }
        
        // Siempre crear wrappers seguros para prevenir errores
        // Esto evita que los errores se muestren en la consola
        const originalLocalStorage = window.localStorage;
        const originalSessionStorage = window.sessionStorage;
        
        // Crear proxies que silencian errores
        try {
            Object.defineProperty(window, 'localStorage', {
                value: new Proxy(originalLocalStorage, {
                    get: function(target, prop) {
                        if (typeof target[prop] === 'function') {
                            return function(...args) {
                                try {
                                    return target[prop].apply(target, args);
                                } catch (e) {
                                    // Silenciar todos los errores de storage
                                    if (prop === 'setItem') return undefined;
                                    if (prop === 'removeItem') return undefined;
                                    if (prop === 'clear') return undefined;
                                    return null;
                                }
                            };
                        }
                        try {
                            return target[prop];
                        } catch (e) {
                            return null;
                        }
                    }
                }),
                writable: false,
                configurable: false
            });
        } catch (e) {
            // Si no se puede sobrescribir, al menos los errores están siendo interceptados
        }
        
        try {
            Object.defineProperty(window, 'sessionStorage', {
                value: new Proxy(originalSessionStorage, {
                    get: function(target, prop) {
                        if (typeof target[prop] === 'function') {
                            return function(...args) {
                                try {
                                    return target[prop].apply(target, args);
                                } catch (e) {
                                    // Silenciar todos los errores de storage
                                    if (prop === 'setItem') return undefined;
                                    if (prop === 'removeItem') return undefined;
                                    if (prop === 'clear') return undefined;
                                    return null;
                                }
                            };
                        }
                        try {
                            return target[prop];
                        } catch (e) {
                            return null;
                        }
                    }
                }),
                writable: false,
                configurable: false
            });
        } catch (e) {
            // Si no se puede sobrescribir, al menos los errores están siendo interceptados
        }
    })();
    
    // Script inline: ocultar preloader cuando la página esté lista
    (function() {
        function hidePreloader() {
            var preloader = document.getElementById('sena-preloader');
            if (preloader) {
                preloader.style.display = 'none';
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
                document.body.classList.add('preloader-ready');
                setTimeout(function() {
                    if (preloader && preloader.parentNode) {
                        preloader.remove();
                    }
                }, 100);
            }
        }

        // Si Livewire está navegando, ocultar inmediatamente
        if (document.documentElement.classList.contains('livewire-navigate-loading')) {
            hidePreloader();
            return;
        }

        // Para carga inicial: ocultar cuando la página esté lista
        if (document.readyState === 'complete') {
            hidePreloader();
        } else if (document.readyState === 'interactive') {
            setTimeout(hidePreloader, 100);
        } else {
            window.addEventListener('load', hidePreloader);
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(hidePreloader, 100);
            });
        }

        // Fallback: ocultar después de 2 segundos
        setTimeout(hidePreloader, 2000);

        // Si Livewire está disponible, escuchar eventos de navegación
        if (typeof window.Livewire !== 'undefined') {
            document.addEventListener('livewire:navigate', hidePreloader);
            document.addEventListener('livewire:navigated', hidePreloader);
        }
    })();
</script>
