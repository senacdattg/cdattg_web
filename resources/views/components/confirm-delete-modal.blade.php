<!-- Modal de Confirmación de Eliminación Unificado -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title font-weight-bold" id="confirmDeleteModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="font-weight-bold text-dark mb-2">¿Está seguro de eliminar este elemento?</h6>
                    <p class="text-muted mb-0">
                        <span id="deleteItemName" class="font-weight-medium text-danger"></span>
                    </p>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Esta acción no se puede revertir
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>
                    Cancelar
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt mr-1"></i>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Función para mostrar el modal de confirmación
    window.showDeleteModal = function(itemName, deleteUrl) {
        $('#deleteItemName').text(itemName);
        $('#deleteForm').attr('action', deleteUrl);
        $('#confirmDeleteModal').modal('show');
    };

    // Función para confirmación con SweetAlert2 (alternativa)
    window.confirmDelete = function(itemName, deleteUrl, formElement = null) {
        Swal.fire({
            title: '¿Eliminar elemento?',
            html: `
                <div class="text-center">
                    <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-2">¿Está seguro de eliminar <strong class="text-danger">${itemName}</strong>?</p>
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Esta acción no se puede revertir
                    </small>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Eliminar',
            cancelButtonText: '<i class="fas fa-times mr-1"></i> Cancelar',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
                popup: 'animated fadeInDown',
                actions: 'gap-2'
            },
            buttonsStyling: false,
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.4)',
            allowOutsideClick: false,
            allowEscapeKey: false,
            padding: '1.5em',
            width: '400px'
        }).then((result) => {
            if (result.isConfirmed) {
                if (formElement) {
                    formElement.submit();
                } else {
                    // Crear y enviar formulario
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        });
    };
});
</script>
