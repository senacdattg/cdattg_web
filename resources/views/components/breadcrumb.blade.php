@props(['items' => []])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
        @foreach($items as $index => $item)
            @if(isset($item['active']) && $item['active'])
                <li class="breadcrumb-item active" aria-current="page">
                    @if(isset($item['icon']))
                        <i class="fas {{ $item['icon'] }}"></i>
                    @endif
                    {{ $item['label'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] ?? '#' }}" class="link_right_header" wire:navigate>
                        @if(isset($item['icon']))
                            <i class="fas {{ $item['icon'] }}"></i>
                        @endif
                        {{ $item['label'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
