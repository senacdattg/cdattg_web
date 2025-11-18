import '../css/style.css';
import './bootstrap';
import './modules/storage-safe';
import './modules/confirm-delete-modal';
import './modules/fullscreen-remember';
import './modules/preloader';
import './modules/sidebar-persist';
import './modules/tracking-prevention-suppress';
import './modules/livewire-logout-fix';
import { suppressTrackingPreventionErrors } from './modules/tracking-prevention-suppress';
import { getGlobalAlertHandler } from './modules/alert-handler';

// Suprimir errores de Tracking Prevention globalmente
suppressTrackingPreventionErrors();

// Exponer instancia global de AlertHandler
window.AlertHandler = getGlobalAlertHandler();

