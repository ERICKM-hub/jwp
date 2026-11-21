@props([
    'name' => 'corporate',
])

@php
    $paths = [
        'corporate' => 'M3 7.5A1.5 1.5 0 0 1 4.5 6h15A1.5 1.5 0 0 1 21 7.5v9a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 16.5v-9ZM7 10h4M7 13h10',
        'legal-research' => 'M7 4h7l4 4v12H7V4Zm7 0v4h4M9 12h6M9 15h6',
        'compliance' => 'M9 12l2 2 4-4M5 7h14v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7Zm3-3h8l1 3H7l1-3Z',
        'procurement' => 'M4 7h16M6 7V5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v2M5 7l1.2 12.2A1.5 1.5 0 0 0 7.7 20.5h8.6a1.5 1.5 0 0 0 1.5-1.3L19 7',
        'documentation' => 'M8 4h6l4 4v12H8V4Zm6 0v4h4M10 12h6M10 15h6M10 18h4',
        'brand' => 'M12 3l2.2 6.6H21l-5.4 3.9 2.1 6.5L12 16.8 6.3 20l2.1-6.5L3 9.6h6.8L12 3Z',
        'esg' => 'M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9Zm0-14v5l3 2',
        'training' => 'M4 19h16M7 19V9l5-3 5 3v10M10 12h4M10 15h4',
    ];
    $d = $paths[$name] ?? $paths['corporate'];
@endphp

<svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24', 'stroke-width' => '1.75']) }} aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $d }}" />
</svg>
