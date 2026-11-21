@props([
    'index' => 0,
])

@php
    $paths = [
        'M12 6v6l4 2M12 22a10 10 0 100-20 10 10 0 000 20z',
        'M3 17l6-6 4 4 8-8M21 21H3',
        'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z',
        'M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z',
    ];
    $path = $paths[$index % count($paths)];
@endphp

<svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.75', 'aria-hidden' => 'true']) }}>
    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
</svg>
