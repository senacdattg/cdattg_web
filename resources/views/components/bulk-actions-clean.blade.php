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
    @vite(['resources/js/modules/bulk-actions.js'])
@endpush
