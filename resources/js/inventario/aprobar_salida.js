document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalAprobarRechazar');
    const modalInfo = document.getElementById('modalDetalleOrden');
    const form = document.getElementById('formAprobarSalida');
    const mensaje = document.getElementById('mensajeAprobacion');
    const btnAprobar = document.querySelector('.btn_aprobar');
    const btnRechazar = document.querySelector('.btn_rechazar');
    const divMotivo = document.querySelector('.div_motivo_rechazo');

    if (btnRechazar && divMotivo) {
        btnRechazar.addEventListener('click', function(e) {
            e.preventDefault();
            divMotivo.style.display = 'block';
            document.getElementById('motivo_rechazo').classList.add('rechazo-activo');
            document.getElementById('iconAprobarRechazar').innerHTML = '<i class="fas fa-times" style="color:#dc3545;"></i>';
        });
    }
    if (btnAprobar && divMotivo) {
        btnAprobar.addEventListener('click', function(e) {
            e.preventDefault();
            divMotivo.style.display = 'none';
            document.getElementById('motivo_rechazo').classList.remove('rechazo-activo');
            document.getElementById('iconAprobarRechazar').innerHTML = '<i class="fas fa-check" style="color:#28a745;"></i>';
            mensaje.innerHTML = '<div class="msg-success">Salida de orden aprobada correctamente.</div>';
            setTimeout(function(){
                modal.classList.remove('show');
                mensaje.innerHTML = '';
                form.reset();
            }, 1500);
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (divMotivo && divMotivo.style.display === 'block') {
                const motivo = document.getElementById('motivo_rechazo').value.trim();
                if (!motivo) {
                    mensaje.innerHTML = '<div class="msg-error">Por favor, escribe el motivo del rechazo.</div>';
                    return;
                }
                mensaje.innerHTML = '<div class="msg-success">La orden ha sido rechazada.<br><strong>Motivo:</strong> ' + motivo + '</div>';
                setTimeout(function(){
                    modal.classList.remove('show');
                    mensaje.innerHTML = '';
                    form.reset();
                    divMotivo.style.display = 'none';
                }, 1500);
            }
        });
    }

    // Cerrar modal info orden al hacer click fuera del contenido
    function cerrarModal(modalElement) {
        if (modalElement) {
            modalElement.addEventListener('mousedown', function(e) {
                if (e.target === modalElement) {
                    modalElement.classList.remove('show');
                }
            });
        }
    }
    cerrarModal(modal);
    cerrarModal(modalInfo);

    // Funcionalidad para tooltip de información de usuario
    console.log('Inicializando tooltips de usuario...');
    
    // Esperamos un poco para asegurarnos de que el modal esté listo
    setTimeout(function() {
        const userIcons = document.querySelectorAll('.user-info-icon');
        console.log('Iconos de usuario encontrados:', userIcons.length);
        
        const userInfoCache = new Map(); // Cache para evitar múltiples peticiones

        userIcons.forEach((icon, index) => {
            console.log('Configurando ícono:', index);
            
            icon.addEventListener('mouseenter', function() {
                console.log('Mouse enter en ícono');
                const userId = this.getAttribute('data-usuario-id');
                console.log('ID de usuario:', userId);
                
                if (userId) {
                    loadUserInfo(userId, this);
                } else {
                    console.log('ID de usuario no válido');
                }
            });

            // Evento para ocultar tooltip cuando sale del icono
            icon.addEventListener('mouseleave', function() {
                console.log('Mouse leave del ícono');
                const tooltip = this.closest('.user-field').querySelector('.user-tooltip');
                if (tooltip) {
                    tooltip.classList.remove('show');
                    console.log('Tooltip ocultado');
                }
            });

            // También agregar un click para prueba
            icon.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Click en ícono de usuario');
                const tooltip = this.closest('.user-field').querySelector('.user-tooltip');
                if (tooltip) {
                    tooltip.classList.toggle('show');
                    console.log('Tooltip toggled');
                }
            });
        });

        function loadUserInfo(userId, iconElement) {
            console.log('Cargando info para usuario:', userId);
            
            // Buscar el tooltip correspondiente
            const tooltip = iconElement.closest('.user-field').querySelector('.user-tooltip');
            console.log('Tooltip encontrado:', !!tooltip);
            
            if (!tooltip) {
                console.error('No se encontró el tooltip');
                return;
            }

            // Si ya tenemos la información en cache, mostrarla
            if (userInfoCache.has(userId)) {
                updateTooltipContent(tooltip, userInfoCache.get(userId));
                return;
            }

            // Datos simulados mejorados para demostración
            const userData = {
                1: {
                    id: '1',
                    name: 'Juan Carlos Pérez',
                    email: 'juan.perez@sena.edu.co',
                    role: 'Administrador',
                    status: 'Activo',
                    lastLogin: '21/08/2025 14:30'
                },
                2: {
                    id: '2',
                    name: 'Ana María Gómez',
                    email: 'ana.gomez@sena.edu.co',
                    role: 'Supervisor',
                    status: 'Activo',
                    lastLogin: '21/08/2025 13:45'
                },
                3: {
                    id: '3',
                    name: 'Carlos Alberto Ruiz',
                    email: 'carlos.ruiz@sena.edu.co',
                    role: 'Instructor',
                    status: 'Activo',
                    lastLogin: '21/08/2025 12:15'
                }
            };

            const userInfo = userData[userId] || {
                id: userId,
                name: `Usuario ${userId}`,
                email: `usuario${userId}@sena.edu.co`,
                role: 'Usuario',
                status: 'Activo',
                lastLogin: new Date().toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                })
            };

            // Guardar en cache
            userInfoCache.set(userId, userInfo);
            
            // Actualizar tooltip
            updateTooltipContent(tooltip, userInfo);
        }

        function updateTooltipContent(tooltip, userData) {
            console.log('Actualizando contenido del tooltip');
            
            const userIdSpan = tooltip.querySelector('.user-id');
            const userNameSpan = tooltip.querySelector('.user-name');
            const userEmailSpan = tooltip.querySelector('.user-email');
            const userRoleSpan = tooltip.querySelector('.user-role');
            const userStatusSpan = tooltip.querySelector('.user-status');
            const userLastLoginSpan = tooltip.querySelector('.user-last-login');

            if (userIdSpan) userIdSpan.textContent = userData.id;
            if (userNameSpan) userNameSpan.textContent = userData.name;
            if (userEmailSpan) userEmailSpan.textContent = userData.email;
            if (userRoleSpan) userRoleSpan.textContent = userData.role;
            if (userStatusSpan) userStatusSpan.textContent = userData.status;
            if (userLastLoginSpan) userLastLoginSpan.textContent = userData.lastLogin;
            
            // Mostrar el tooltip usando clase
            tooltip.classList.add('show');
            
            console.log('Contenido actualizado y tooltip mostrado con clase');
        }
    }, 1000);

    // Función de prueba global
    window.testTooltips = function() {
        console.log('=== PROBANDO TOOLTIPS ===');
        const userIcons = document.querySelectorAll('.user-info-icon');
        console.log('Iconos encontrados:', userIcons.length);
        
        if (userIcons.length === 0) {
            alert('No se encontraron iconos de usuario. Asegúrate de que el modal esté abierto.');
            return;
        }
        
        userIcons.forEach((icon, index) => {
            console.log(`Ícono ${index}:`, icon);
            console.log(`Data-usuario-id:`, icon.getAttribute('data-usuario-id'));
            
            const userField = icon.closest('.user-field');
            console.log(`User field ${index}:`, userField);
            
            const tooltip = userField ? userField.querySelector('.user-tooltip') : null;
            console.log(`Tooltip ${index}:`, tooltip);
            
            if (tooltip) {
                // Simular carga de datos
                const userId = icon.getAttribute('data-usuario-id');
                const userData = {
                    1: {
                        id: '1',
                        name: 'Juan Carlos Pérez',
                        email: 'juan.perez@sena.edu.co',
                        role: 'Administrador',
                        status: 'Activo',
                        lastLogin: '21/08/2025 14:30'
                    },
                    2: {
                        id: '2',
                        name: 'Ana María Gómez',
                        email: 'ana.gomez@sena.edu.co',
                        role: 'Supervisor',
                        status: 'Activo',
                        lastLogin: '21/08/2025 13:45'
                    }
                };
                
                const userInfo = userData[userId] || {
                    id: userId,
                    name: `Usuario ${userId}`,
                    email: `usuario${userId}@sena.edu.co`,
                    role: 'Usuario',
                    status: 'Activo',
                    lastLogin: '21/08/2025 15:00'
                };
                
                // Actualizar contenido
                const userIdSpan = tooltip.querySelector('.user-id');
                const userNameSpan = tooltip.querySelector('.user-name');
                const userEmailSpan = tooltip.querySelector('.user-email');
                const userRoleSpan = tooltip.querySelector('.user-role');
                const userStatusSpan = tooltip.querySelector('.user-status');
                const userLastLoginSpan = tooltip.querySelector('.user-last-login');

                if (userIdSpan) userIdSpan.textContent = userInfo.id;
                if (userNameSpan) userNameSpan.textContent = userInfo.name;
                if (userEmailSpan) userEmailSpan.textContent = userInfo.email;
                if (userRoleSpan) userRoleSpan.textContent = userInfo.role;
                if (userStatusSpan) userStatusSpan.textContent = userInfo.status;
                if (userLastLoginSpan) userLastLoginSpan.textContent = userInfo.lastLogin;
                
                // Mostrar tooltip temporalmente
                tooltip.classList.add('show');
                console.log(`Tooltip ${index} mostrado`);
                
                setTimeout(() => {
                    tooltip.classList.remove('show');
                    console.log(`Tooltip ${index} ocultado`);
                }, 3000);
            }
        });
        
        alert('Tooltips de prueba mostrados. Revisa la consola para más detalles.');
    };

});
