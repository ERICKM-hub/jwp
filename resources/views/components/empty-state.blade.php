@props([
    'title',
    'text' => null,
    'actionHref' => null,
    'actionLabel' => null,
])

<div {{ $attributes->class(['rounded-lg border border-dashed border-brand-navy/20 bg-brand-surface/70 px-5 py-8 text-center sm:px-6 sm:py-10']) }}>
    <h2 class="text-lg font-semibold text-brand-navy sm:text-xl">{{ $title }}</h2>
    @if ($text)
        <p class="mx-auto mt-2 max-w-lg text-sm leading-relaxed text-brand-ink/70">
            {{ $text }}
        </p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mx-auto mt-2 max-w-lg text-sm leading-relaxed text-brand-ink/70">
            {{ $slot }}
        </div>
    @endif
    @if ($actionHref && $actionLabel)
        <a href="{{ $actionHref }}" class="btn-primary mt-5">
            {{ $actionLabel }}
        </a>
    @endif
</div>
