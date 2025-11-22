{{-- CSS crítico inline para el preloader - minificado para carga rápida --}}
<style>
    #sena-preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 99999;
        transition: opacity .5s, visibility .5s
    }

    #sena-preloader.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        display: none !important;
    }

    #sena-preloader img {
        width: 80px;
        height: 80px;
        animation: preloader-shake 1s ease-in-out infinite;
        margin-bottom: 20px
    }

    @keyframes preloader-shake {

        0%,
        100% {
            transform: translateX(0) rotate(0)
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-5px) rotate(-5deg)
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(5px) rotate(5deg)
        }
    }

    #sena-preloader .sena-preloader-text {
        color: #39b54a;
        font-size: 16px;
        font-weight: 500;
        margin-top: 10px
    }

    body:not(.preloader-ready) {
        overflow: hidden
    }

    /* Ocultar preloader durante navegación SPA con Livewire - REGLAS AGRESIVAS */
    html.livewire-navigate-loading #sena-preloader,
    html.livewire-navigate-loading body #sena-preloader,
    body.preloader-ready #sena-preloader,
    body[data-preloader-hidden] #sena-preloader {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        z-index: -1 !important;
    }
</style>
