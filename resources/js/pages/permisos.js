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
        moverElemento(permissionName, fromList, toList, 'permissions', action);
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
        // Búsqueda para permisos
        const searchAvailablePerm = document.querySelector('.search-available');
        const searchAssignedPerm = document.querySelector('.search-assigned');

        if (searchAvailablePerm) {
            searchAvailablePerm.addEventListener('input', function() {
                filtrarLista('available-permissions', this.value);
            });
        }

        if (searchAssignedPerm) {
            searchAssignedPerm.addEventListener('input', function() {
                filtrarLista('assigned-permissions', this.value);
            });
        }

        // Búsqueda para roles
        const searchAvailableRoles = document.querySelector('.search-available-roles');
        const searchAssignedRoles = document.querySelector('.search-assigned-roles');

        if (searchAvailableRoles) {
            searchAvailableRoles.addEventListener('input', function() {
                filtrarLista('available-roles', this.value);
            });
        }

        if (searchAssignedRoles) {
            searchAssignedRoles.addEventListener('input', function() {
                filtrarLista('assigned-roles', this.value);
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

    // Función para actualizar roles
    async function actualizarRol(roleName, action) {
        const url = action === 'assign'
            ? `/api/roles/asignar/${userId}/${roleName}`
            : `/api/roles/remover/${userId}/${roleName}`;

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
                alertHandler.showError(response.data.message || 'Error al actualizar el rol');
                return false;
            }
        } catch (error) {
            console.error('Error:', error);

            let message = 'Error al actualizar el rol';
            if (error.response && error.response.data && error.response.data.message) {
                message = error.response.data.message;
            }

            alertHandler.showError(message);

            // Revertir el cambio visual
            const reverseAction = action === 'assign' ? 'remove' : 'assign';
            moverElemento(roleName,
                action === 'assign' ? 'assigned' : 'available',
                action === 'assign' ? 'available' : 'assigned',
                'roles',
                reverseAction);

            return false;
        }
    }

    // Función para mover elementos (permisos o roles)
    function moverElemento(itemName, fromList, toList, type, action) {
        const listType = type === 'roles' ? 'roles' : 'permissions';
        const availableList = document.getElementById(`available-${listType}`);
        const assignedList = document.getElementById(`assigned-${listType}`);

        const sourceList = fromList === 'available' ? availableList : assignedList;
        const item = sourceList.querySelector(`[data-value="${itemName}"]`);

        if (!item) return;

        // Clonar el elemento
        const newItem = item.cloneNode(true);

        // Actualizar el name del checkbox según la nueva lista
        const checkbox = newItem.querySelector('input[type="checkbox"]');
        if (toList === 'assigned') {
            checkbox.setAttribute('name', type === 'roles' ? 'roles[]' : 'permissions[]');
            checkbox.checked = true;
        } else {
            checkbox.setAttribute('name', type === 'roles' ? 'available_roles[]' : 'available_permissions[]');
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

    // Event listeners para los botones de flecha de ROLES
    document.getElementById('move-right-roles').addEventListener('click', async () => {
        const availableList = document.getElementById('available-roles');
        const checkedItems = availableList.querySelectorAll('input[type="checkbox"]:checked');

        if (checkedItems.length === 0) {
            alertHandler.showWarning('Selecciona al menos un rol para asignar');
            return;
        }

        // Procesar cada rol seleccionado
        for (const checkbox of checkedItems) {
            const roleName = checkbox.value;

            // Mover visualmente primero
            moverElemento(roleName, 'available', 'assigned', 'roles', 'assign');

            // Luego actualizar en el servidor
            const success = await actualizarRol(roleName, 'assign');
            if (!success) {
                // El error ya fue manejado en actualizarRol
                break;
            }
        }
    });

    document.getElementById('move-left-roles').addEventListener('click', async () => {
        const assignedList = document.getElementById('assigned-roles');
        const checkedItems = assignedList.querySelectorAll('input[type="checkbox"]:checked');

        if (checkedItems.length === 0) {
            alertHandler.showWarning('Selecciona al menos un rol para remover');
            return;
        }

        // Procesar cada rol seleccionado
        for (const checkbox of checkedItems) {
            const roleName = checkbox.value;

            // Mover visualmente primero
            moverElemento(roleName, 'assigned', 'available', 'roles', 'remove');

            // Luego actualizar en el servidor
            const success = await actualizarRol(roleName, 'remove');
            if (!success) {
                // El error ya fue manejado en actualizarRol
                break;
            }
        }
    });

    // Funcionalidad de selección múltiple para ROLES
    document.getElementById('select-all-available-roles').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#available-roles input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('select-all-assigned-roles').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#assigned-roles input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Inicializar búsqueda
    actualizarBusqueda();

    console.log('Funcionalidad de permisos y roles en tiempo real inicializada');
});