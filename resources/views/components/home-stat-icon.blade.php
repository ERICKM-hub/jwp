@props([
    'name' => 'briefcase',
])

@php
    $paths = [
        'briefcase' => 'M4 7h16v12H4V7zm2-3h12v3H6V4zm4 8h4',
        'users' => 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm8 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
        'chart' => 'M4 19V5M4 19h16M8 17V9m4 8V7m4 10v-4',
        'globe' => 'M12 2a10 10 0 100 20 10 10 0 000-20zm0 0v20M2 12h20',
    ];
    $path = $paths[$name] ?? $paths['briefcase'];
@endphp

<svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.75', 'aria-hidden' => 'true']) }}>
    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
</svg>
