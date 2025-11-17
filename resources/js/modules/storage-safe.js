/**
 * Módulo para manejar localStorage y sessionStorage de forma segura
 * Maneja errores de Tracking Prevention y otros bloqueos del navegador
 */

(function () {
    'use strict';

    /**
     * Verifica si el almacenamiento está disponible
     */
    function isStorageAvailable(type) {
        try {
            const storage = window[type];
            const x = '__storage_test__';
            storage.setItem(x, x);
            storage.removeItem(x);
            return true;
        } catch (e) {
            return false;
        }
    }

    /**
     * Wrapper seguro para localStorage.setItem
     */
    function safeSetItem(key, value, storageType = 'localStorage') {
        try {
            if (!isStorageAvailable(storageType)) {
                return false;
            }
            const storage = window[storageType];
            storage.setItem(key, value);
            return true;
        } catch (e) {
            // Silenciar errores de Tracking Prevention
            if (e.name === 'QuotaExceededError' || e.name === 'SecurityError') {
                console.warn(`No se pudo guardar en ${storageType}:`, e.message);
            }
            return false;
        }
    }

    /**
     * Wrapper seguro para localStorage.getItem
     */
    function safeGetItem(key, storageType = 'localStorage', defaultValue = null) {
        try {
            if (!isStorageAvailable(storageType)) {
                return defaultValue;
            }
            const storage = window[storageType];
            const value = storage.getItem(key);
            return value !== null ? value : defaultValue;
        } catch (e) {
            // Silenciar errores de Tracking Prevention
            if (e.name === 'SecurityError') {
                console.warn(`No se pudo leer de ${storageType}:`, e.message);
            }
            return defaultValue;
        }
    }

    /**
     * Wrapper seguro para localStorage.removeItem
     */
    function safeRemoveItem(key, storageType = 'localStorage') {
        try {
            if (!isStorageAvailable(storageType)) {
                return false;
            }
            const storage = window[storageType];
            storage.removeItem(key);
            return true;
        } catch (e) {
            // Silenciar errores de Tracking Prevention
            if (e.name === 'SecurityError') {
                console.warn(`No se pudo eliminar de ${storageType}:`, e.message);
            }
            return false;
        }
    }

    /**
     * Wrapper seguro para localStorage.clear
     */
    function safeClear(storageType = 'localStorage') {
        try {
            if (!isStorageAvailable(storageType)) {
                return false;
            }
            const storage = window[storageType];
            storage.clear();
            return true;
        } catch (e) {
            // Silenciar errores de Tracking Prevention
            if (e.name === 'SecurityError') {
                console.warn(`No se pudo limpiar ${storageType}:`, e.message);
            }
            return false;
        }
    }

    // Exportar funciones globalmente
    window.StorageSafe = {
        setItem: safeSetItem,
        getItem: safeGetItem,
        removeItem: safeRemoveItem,
        clear: safeClear,
        isAvailable: isStorageAvailable,
    };
})();

