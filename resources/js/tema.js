// Double-click handlers for both select boxes
document.getElementById('parametros_disponibles').addEventListener('dblclick', function () {
    moveSelectedOptions('parametros_disponibles', 'parametros_asignados');
});

document.getElementById('parametros_asignados').addEventListener('dblclick', function () {
    moveSelectedOptions('parametros_asignados', 'parametros_disponibles');
});

// Button click handlers
document.getElementById('agregar_parametro').addEventListener('click', function () {
    moveSelectedOptions('parametros_disponibles', 'parametros_asignados');
});

document.getElementById('quitar_parametro').addEventListener('click', function () {
    moveSelectedOptions('parametros_asignados', 'parametros_disponibles');
});

// Manejador de clic para deseleccionar al hacer clic fuera
document.addEventListener('click', function (e) {
    const parametrosDisponibles = document.getElementById('parametros_disponibles');
    const parametrosAsignados = document.getElementById('parametros_asignados');

    // Verificar si el clic fue en un botón de guardar o dentro de los selectores
    const isSubmitButton = e.target.closest('button[type="submit"]') ||
        e.target.closest('.btn-primary');
    const isSelectArea = e.target.closest('.form-group');

    if (!isSelectArea && !isSubmitButton) {
        parametrosDisponibles.querySelectorAll('option:checked').forEach(option => {
            option.selected = false;
        });
        parametrosAsignados.querySelectorAll('option:checked').forEach(option => {
            option.selected = false;
        });
    }

    // Asegurar que todas las opciones en parametros_asignados estén seleccionadas antes de enviar
    if (isSubmitButton) {
        Array.from(parametrosAsignados.options).forEach(option => {
            option.selected = true;
        });
    }
});

function moveSelectedOptions(fromSelectId, toSelectId) {
    const fromSelect = document.getElementById(fromSelectId);
    const toSelect = document.getElementById(toSelectId);

    // If nothing is selected, select the clicked item
    if (fromSelect.selectedOptions.length === 0 && fromSelect.options.length > 0) {
        fromSelect.options[fromSelect.options.length - 1].selected = true;
    }

    // Move all selected options
    for (let option of [...fromSelect.selectedOptions]) {
        toSelect.appendChild(option);
    }

    // Sort options alphabetically
    sortSelect(toSelect);
}

function sortSelect(select) {
    let options = [...select.options];
    options.sort((a, b) => a.text.localeCompare(b.text));
    options.forEach(option => select.appendChild(option));
}