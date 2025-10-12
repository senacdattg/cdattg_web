{{--
    Componente: Estilos CSS para Historial de Asignaciones
    Descripción: Estilos del timeline para el historial de asignaciones
    
    Uso:
    @include('components.historial-asignaciones-styles')
--}}

<style>
    /* Estilos para timeline de logs */
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -35px;
        top: 15px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
    }
    
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -29px;
        top: 27px;
        width: 2px;
        height: calc(100% + 10px);
        background-color: #dee2e6;
    }
    
    .timeline-content {
        margin-left: 0;
    }
    
    /* Estilos para borders coloreados */
    .border-left-primary {
        border-left: 0.25rem solid #007bff !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #28a745 !important;
    }
    
    .border-left-warning {
        border-left: 0.25rem solid #ffc107 !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #17a2b8 !important;
    }
    
    .border-left-danger {
        border-left: 0.25rem solid #dc3545 !important;
    }
</style>

