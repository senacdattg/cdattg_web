@props([
    'actions' => [],
    'selectAll' => true,
    'selectAllText' => 'Seleccionar todo',
    'bulkActionsText' => 'Acciones masivas',
    'showCount' => true
])

<div class="bulk-actions-container d-none">
    <div class="d-flex align-items-center justify-content-between p-3 bg-light border-bottom">
        <div class="d-flex align-items-center">
            @if($selectAll)
                <div class="form-check mr-3">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        {{ $selectAllText }}
                    </label>
                </div>
            @endif
            
            @if($showCount)
                <span class="text-muted mr-3">
                    <span class="selected-count">0</span> elementos seleccionados
                </span>
            @endif
        </div>
        
        <div class="d-flex align-items-center">
            <span class="text-muted mr-2">{{ $bulkActionsText }}:</span>
            <div class="btn-group">
                @foreach($actions as $action)
                    <button type="button" 
                            class="btn btn-sm {{ $action['class'] ?? 'btn-outline-secondary' }} bulk-action-btn"
                            data-action="{{ $action['action'] }}"
                            data-url="{{ $action['url'] ?? '' }}"
                            data-confirm="{{ $action['confirm'] ?? false }}"
                            data-confirm-message="{{ $action['confirm_message'] ?? '' }}"
                            {{ isset($action['permission']) ? "data-permission=\"{$action['permission']}\"" : '' }}>
                        @if(isset($action['icon']))
                            <i class="fas {{ $action['icon'] }}"></i>
                        @endif
                        {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('input[type="checkbox"][name="selected_items[]"]');
    const bulkActionsContainer = document.querySelector('.bulk-actions-container');
    const selectedCountSpan = document.querySelector('.selected-count');
    const bulkActionBtns = document.querySelectorAll('.bulk-action-btn');
    
    function updateBulkActions() {
        const selectedCount = document.querySelectorAll('input[type="checkbox"][name="selected_items[]"]:checked').length;
        
        if (selectedCount > 0) {
            bulkActionsContainer.classList.remove('d-none');
            selectedCountSpan.textContent = selectedCount;
        } else {
            bulkActionsContainer.classList.add('d-none');
        }
        
        // Actualizar estado del checkbox "Seleccionar todo"
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = selectedCount === rowCheckboxes.length && rowCheckboxes.length > 0;
            selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < rowCheckboxes.length;
        }
    }
    
    // Event listener para checkbox "Seleccionar todo"
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }
    
    // Event listeners para checkboxes de filas
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    // Event listeners para botones de acciones masivas
    bulkActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedItems = Array.from(document.querySelectorAll('input[type="checkbox"][name="selected_items[]"]:checked'))
                .map(checkbox => checkbox.value);
            
            if (selectedItems.length === 0) {
                alert('Por favor selecciona al menos un elemento');
                return;
            }
            
            const action = this.dataset.action;
            const url = this.dataset.url;
            const confirm = this.dataset.confirm === 'true';
            const confirmMessage = this.dataset.confirmMessage;
            
            if (confirm && !window.confirm(confirmMessage || '¿Estás seguro de realizar esta acción?')) {
                return;
            }
            
            // Aquí puedes implementar la lógica para cada acción
            console.log('Acción:', action, 'Elementos:', selectedItems, 'URL:', url);
            
            // Ejemplo de implementación para eliminación masiva
            if (action === 'delete' && url) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                selectedItems.forEach(item => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'items[]';
                    input.value = item;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>
@endpush
