/**
 * Script para gestión de notificaciones
 * Incluye funcionalidades de marcar como leída, eliminar y vaciar todas
 */

$(document).ready(function() {
    
    // Marcar notificación como leída
    $('.mark-read').on('click', function() {
        const notificationId = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: `/inventario/notificaciones/${notificationId}/read`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    // Cambiar el botón por un badge
                    button.closest('.list-group-item').removeClass('list-group-item-light');
                    button.replaceWith('<span class="badge badge-success mb-1" title="Leída"><i class="fas fa-check"></i></span>');
                    
                    // Mostrar notificación de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Listo!',
                        text: 'Notificación marcada como leída',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo marcar la notificación como leída',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    });
    
    // Abrir recurso relacionado con la notificación
    $('.open-notification').on('click', function(event) {
        event.preventDefault();
        const targetUrl = $(this).data('url');
        const notificationId = $(this).data('id');
        const isUnread = $(this).data('unread') === true || $(this).data('unread') === 'true';

        if (!targetUrl) {
            return;
        }

        const redirectToTarget = () => {
            window.location.href = targetUrl;
        };

        if (!notificationId || !isUnread) {
            redirectToTarget();
            return;
        }

        $.ajax({
            url: `/inventario/notificaciones/${notificationId}/read`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    redirectToTarget();
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo marcar la notificación como leída'
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo marcar la notificación como leída'
                });
            }
        });
    });
    
    // Eliminar notificación individual
    $('.delete-notification').on('click', function() {
        const notificationId = $(this).data('id');
        const listItem = $(this).closest('.list-group-item');
        
        Swal.fire({
            title: '¿Eliminar notificación?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/inventario/notificaciones/${notificationId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        // Animar y remover el elemento
                        listItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Verificar si quedan notificaciones
                            if($('.list-group-item').length === 0) {
                                location.reload();
                            }
                        });
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: 'La notificación ha sido eliminada',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar la notificación'
                        });
                    }
                });
            }
        });
    });
    
    // Vaciar todas las notificaciones
    $('#vaciar-notificaciones').on('click', function() {
        const totalNotifications = $('.list-group-item').length;
        
        if(totalNotifications === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Sin notificaciones',
                text: 'No hay notificaciones para eliminar',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            return;
        }
        
        Swal.fire({
            title: '¿Vaciar todas las notificaciones?',
            html: `Se eliminarán <strong>${totalNotifications}</strong> notificación(es).<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, vaciar todo',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '/inventario/notificaciones/vaciar-todas',
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                }).then(response => {
                    return response;
                }).catch(error => {
                    Swal.showValidationMessage(`Error: ${error.statusText}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: `${result.value.deleted} notificación(es) eliminada(s)`,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    location.reload();
                });
            }
        });
    });
    
    // Marcar todas como leídas
    $('#marcar-todas-leidas').on('click', function() {
        const unreadCount = $('.list-group-item-light').length;
        
        if(unreadCount === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Todo al día',
                text: 'No hay notificaciones sin leer',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            return;
        }
        
        Swal.fire({
            title: '¿Marcar todas como leídas?',
            text: `Se marcarán ${unreadCount} notificación(es) como leídas`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, marcar todas',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/inventario/notificaciones/read-all',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Listo!',
                            text: 'Todas las notificaciones marcadas como leídas',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron marcar las notificaciones'
                        });
                    }
                });
            }
        });
    });
});
