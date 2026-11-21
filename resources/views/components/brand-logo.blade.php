@props([
    'size' => 'md',
    'showFallbackText' => true,
])

@php
    use App\Support\SiteSettings;

    $isLarge = $size === 'lg';
    $heightClass = $isLarge ? 'h-28 w-auto sm:h-32' : 'h-12 w-auto';
    $width = $isLarge ? 164 : 82;
    $height = $isLarge ? 122 : 61;
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-3']) }} data-brand-logo>
    <img
        src="{{ SiteSettings::logoUrl() }}"
        alt="{{ SiteSettings::legalName() }} logo"
        width="{{ $width }}"
        height="{{ $height }}"
        class="{{ $heightClass }} shrink-0"
        onerror="this.classList.add('hidden'); const fb = this.parentElement.querySelector('[data-brand-fallback]'); if (fb) { fb.hidden = false; }"
    >
    @if ($showFallbackText)
        <span
            class="text-sm font-semibold tracking-wide text-brand-navy"
            data-brand-fallback
            hidden
        >
            {{ SiteSettings::shortName() }}
        </span>
    @endif
</span>
