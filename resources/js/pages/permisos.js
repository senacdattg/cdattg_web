/**
 * Funcionalidad para gestión de permisos en tiempo real
 */
import axios from 'axios';
import Swal from 'sweetalert2';
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de alertas con Swal
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 3000,
        alertSelector: '.alert',
        Swal: Swal
    });

    const permissionsContainer = document.getElementById('permissions-container');
    if (!permissionsContainer) return;

    const userId = permissionsContainer.dataset.userId;

    // Función para mover permisos entre listas
    function moverPermiso(permissionName, fromList, toList, action) {
        const availableList = document.getElementById('available-permissions');
        const assignedList = document.getElementById('assigned-permissions');

        // Encontrar el elemento en la lista de origen
        const sourceList = fromList === 'available' ? availableList : assignedList;
        const item = sourceList.querySelector(`[data-value="${permissionName}"]`);

        if (!item) return;

        // Clonar el elemento
        const newItem = item.cloneNode(true);

        // Actualizar el name del checkbox según la nueva lista
        const checkbox = newItem.querySelector('input[type="checkbox"]');
        if (toList === 'assigned') {
            checkbox.setAttribute('name', 'permissions[]');
            checkbox.checked = true;
        } else {
            checkbox.setAttribute('name', 'available_permissions[]');
            checkbox.checked = false;
        }

        // Mover a la lista destino
        const targetList = toList === 'available' ? availableList : assignedList;
        targetList.appendChild(newItem);

        // Remover de la lista origen
        item.remove();

        // Actualizar búsqueda si existe
        actualizarBusqueda();
    }

    // Función para hacer la petición AJAX
    async function actualizarPermiso(permissionName, action) {
        const url = action === 'assign'
            ? `/api/permisos/asignar/${userId}/${permissionName}`
            : `/api/permisos/remover/${userId}/${permissionName}`;
    
        const method = action === 'assign' ? 'post' : 'delete';
    
        try {
            const response = await axios({
                method: method,
                url: url,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                withCredentials: true
            });

            if (response.data.success) {
                alertHandler.showSuccess(response.data.message);
                return true;
            } else {
                alertHandler.showError(response.data.message || 'Error al actualizar el permiso');
                return false;
            }
        } catch (error) {
            console.error('Error:', error);

            let message = 'Error al actualizar el permiso';
            if (error.response && error.response.data && error.response.data.message) {
                message = error.response.data.message;
            }

            alertHandler.showError(message);

            // Revertir el cambio visual
            const reverseAction = action === 'assign' ? 'remove' : 'assign';
            moverPermiso(permissionName,
                action === 'assign' ? 'assigned' : 'available',
                action === 'assign' ? 'available' : 'assigned',
                reverseAction);

            return false;
        }
    }

    // Event listeners para los botones de flecha
    document.getElementById('move-right-permissions').addEventListener('click', async () => {
        const availableList = document.getElementById('available-permissions');
        const checkedItems = availableList.querySelectorAll('input[type="checkbox"]:checked');

        if (checkedItems.length === 0) {
            alertHandler.showWarning('Selecciona al menos un permiso para asignar');
            return;
        }

        // Procesar cada permiso seleccionado
        for (const checkbox of checkedItems) {
            const permissionName = checkbox.value;

            // Mover visualmente primero
            moverPermiso(permissionName, 'available', 'assigned', 'assign');

            // Luego actualizar en el servidor
            const success = await actualizarPermiso(permissionName, 'assign');
            if (!success) {
                // El error ya fue manejado en actualizarPermiso
                break;
            }
        }
    });

    document.getElementById('move-left-permissions').addEventListener('click', async () => {
        const assignedList = document.getElementById('assigned-permissions');
        const checkedItems = assignedList.querySelectorAll('input[type="checkbox"]:checked');

        if (checkedItems.length === 0) {
            alertHandler.showWarning('Selecciona al menos un permiso para remover');
            return;
        }

        // Procesar cada permiso seleccionado
        for (const checkbox of checkedItems) {
            const permissionName = checkbox.value;

            // Mover visualmente primero
            moverPermiso(permissionName, 'assigned', 'available', 'remove');

            // Luego actualizar en el servidor
            const success = await actualizarPermiso(permissionName, 'remove');
            if (!success) {
                // El error ya fue manejado en actualizarPermiso
                break;
            }
        }
    });

    // Funcionalidad de búsqueda
    function actualizarBusqueda() {
        const searchAvailable = document.querySelector('.search-available');
        const searchAssigned = document.querySelector('.search-assigned');

        if (searchAvailable) {
            searchAvailable.addEventListener('input', function() {
                filtrarLista('available-permissions', this.value);
            });
        }

        if (searchAssigned) {
            searchAssigned.addEventListener('input', function() {
                filtrarLista('assigned-permissions', this.value);
            });
        }
    }

    function filtrarLista(listId, searchTerm) {
        const list = document.getElementById(listId);
        const items = list.querySelectorAll('.list-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const matches = text.includes(searchTerm.toLowerCase());
            item.style.display = matches ? '' : 'none';
        });
    }

    // Funcionalidad de selección múltiple
    document.getElementById('select-all-available-permissions').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#available-permissions input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('select-all-assigned-permissions').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#assigned-permissions input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Inicializar búsqueda
    actualizarBusqueda();

    console.log('Funcionalidad de permisos en tiempo real inicializada');
});