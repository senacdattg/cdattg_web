$(document).ready(function() {
    let selectedSpecialties = [];
    
    // Búsqueda de personas
    $('#persona_search').on('input', function() {
        const query = $(this).val().toLowerCase();
        const results = $('#search-results');
        
        if (query.length < 2) {
            results.hide();
            return;
        }
        
        const filtered = window.personasData.filter(persona => 
            persona.nombre.toLowerCase().includes(query) ||
            persona.documento.includes(query) ||
            persona.email.toLowerCase().includes(query) ||
            persona.usuario.toLowerCase().includes(query)
        );
        
        if (filtered.length > 0) {
            results.empty();
            filtered.forEach(persona => {
                results.append(`
                    <div class="search-result-item" data-persona-id="${persona.id}">
                        <div class="person-option">${persona.nombre}</div>
                        <div class="person-details-small">
                            ${persona.tipo_documento}: ${persona.documento} | ${persona.email}
                        </div>
                    </div>
                `);
            });
            results.show();
        } else {
            results.hide();
        }
    });

    // Seleccionar persona desde búsqueda
    $(document).on('click', '.search-result-item', function() {
        const personaId = $(this).data('persona-id');
        $('#persona_id').val(personaId).trigger('change');
        $('#persona_search').val('');
        $('#search-results').hide();
    });

    // Cambio en select de persona
    $('#persona_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const info = $('#selected-person-info');
        const name = $('#selected-person-name');
        const details = $('#selected-person-details');
        
        if ($(this).val()) {
            const personaNombre = selectedOption.data('nombre');
            const personaDocumento = selectedOption.data('documento');
            const personaEmail = selectedOption.data('email');
            const personaUsuario = selectedOption.data('usuario');
            
            name.text(personaNombre);
            details.html(`
                <div><i class="fas fa-id-card mr-1"></i> Documento: ${personaDocumento}</div>
                <div><i class="fas fa-envelope mr-1"></i> Email: ${personaEmail}</div>
                <div><i class="fas fa-user mr-1"></i> Usuario: ${personaUsuario}</div>
            `);
            info.show();
            updateFloatingButton();
        } else {
            info.hide();
            updateFloatingButton();
        }
    });

    // Selección de especialidades
    $('#especialidades').on('change', function() {
        updateSelectedSpecialties();
    });

    function updateSelectedSpecialties() {
        const selectedOptions = $('#especialidades option:selected');
        const display = $('#selected-specialties-display');
        
        if (selectedOptions.length > 0) {
            let html = '';
            selectedOptions.each(function() {
                const specialtyName = $(this).text();
                const specialtyId = $(this).val();
                html += `
                    <span class="badge badge-primary mr-1 mb-1">
                        ${specialtyName}
                    </span>
                `;
            });
            display.html(html);
        } else {
            display.html('<span class="text-muted">Ninguna especialidad seleccionada</span>');
        }
    }

    // Validación del botón flotante
    function updateFloatingButton() {
        const personaId = $('#persona_id').val();
        const regionalId = $('#regional_id').val();
        const btn = $('#floating-save-btn');
        
        if (personaId && regionalId) {
            btn.prop('disabled', false);
        } else {
            btn.prop('disabled', true);
        }
    }

    // Cambios en selects para validar botón
    $('#persona_id, #regional_id').on('change', updateFloatingButton);

    // Botón flotante
    $('#floating-save-btn').on('click', function() {
        const personaId = $('#persona_id').val();
        const regionalId = $('#regional_id').val();
        
        if (!personaId) {
            showAlert('Debe seleccionar una persona', 'error');
            return;
        }
        
        if (!regionalId) {
            showAlert('Debe seleccionar una regional', 'error');
            return;
        }
        
        Swal.fire({
            title: '¿Asignar como Instructor?',
            text: '¿Está seguro de que desea asignar el rol de instructor a esta persona?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#instructorForm').submit();
            }
        });
    });

    // Función para mostrar alertas
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${icon} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        
        $('.content').prepend(alert);
        
        setTimeout(() => {
            alert.alert('close');
        }, 3000);
    }

    // Ocultar resultados de búsqueda al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-box').length) {
            $('#search-results').hide();
        }
    });

    // Inicializar
    updateFloatingButton();
});
