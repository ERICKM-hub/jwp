@props([
    'eyebrow' => null,
    'title',
    'lede' => null,
    'actionHref' => null,
    'actionLabel' => null,
])

<div {{ $attributes->class(['flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="max-w-2xl space-y-2">
        @if ($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h2 class="heading-section">{{ $title }}</h2>
        @if ($lede)
            <p class="text-sm leading-relaxed text-brand-ink/75 sm:text-base">{{ $lede }}</p>
        @endif
    </div>

    @if ($actionHref && $actionLabel)
        <a href="{{ $actionHref }}" class="link-quiet shrink-0 text-sm">
            {{ $actionLabel }}
        </a>
    @endif
</div>
