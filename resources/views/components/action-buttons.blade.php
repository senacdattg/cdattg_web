@props([
    'actions' => [],
    'size' => 'sm',
    'groupClass' => 'btn-group',
    'vertical' => false
])

@php
    $groupClass = $vertical ? 'btn-group-vertical btn-group-' . $size : $groupClass . ' btn-group-' . $size;
@endphp

<div class="{{ $groupClass }}" role="group">
    @foreach($actions as $action)
        @if($action['type'] === 'link')
            <a href="{{ $action['url'] }}" 
               class="btn {{ $action['class'] ?? 'btn-light' }} btn-{{ $size }}" 
               data-toggle="tooltip" 
               title="{{ $action['title'] ?? '' }}"
               @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
               @if(isset($action['disabled']) && $action['disabled']) disabled @endif>
                <i class="{{ $action['icon'] }}"></i>
            </a>
        @elseif($action['type'] === 'form')
            <form action="{{ $action['url'] }}" method="POST" class="d-inline">
                @csrf
                @if(isset($action['method']) && $action['method'] !== 'POST')
                    @method($action['method'])
                @endif
                <button type="submit" 
                        class="btn {{ $action['class'] ?? 'btn-light' }} btn-{{ $size }}" 
                        data-toggle="tooltip" 
                        title="{{ $action['title'] ?? '' }}"
                        @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                        @if(isset($action['disabled']) && $action['disabled']) disabled @endif>
                    <i class="{{ $action['icon'] }}"></i>
                </button>
            </form>
        @elseif($action['type'] === 'button')
            <button type="button" 
                    class="btn {{ $action['class'] ?? 'btn-light' }} btn-{{ $size }}" 
                    data-toggle="tooltip" 
                    title="{{ $action['title'] ?? '' }}"
                    @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                    @if(isset($action['disabled']) && $action['disabled']) disabled @endif>
                <i class="{{ $action['icon'] }}"></i>
            </button>
        @endif
    @endforeach
</div>
