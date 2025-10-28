@props([
    'message' => 'No hay registros disponibles',
    'icon' => 'fa-inbox',
    'iconSize' => '3rem',
    'image' => null,
    'imageSize' => '120px',
    'colspan' => 1
])

<tr>
    <td colspan="{{ $colspan }}" class="text-center py-5">
        @if($image)
            <img src="{{ asset($image) }}" alt="No data" style="width: {{ $imageSize }}" class="mb-3">
        @else
            <i class="fas {{ $icon }} text-muted mb-3" style="font-size: {{ $iconSize }};"></i>
        @endif
        <p class="text-muted">{{ $message }}</p>
    </td>
</tr>
