@props([
    'label' => null,
])

<div {{ $attributes->class(['rounded-lg border border-dashed border-brand-navy/20 bg-brand-navy/[0.03] px-4 py-3']) }}>
    @if ($label)
        <p class="text-xs font-semibold uppercase tracking-wider text-brand-gold">{{ $label }}</p>
    @endif
    <p @class(['text-sm italic text-brand-ink/75', 'mt-1' => $label])>
        {{ $slot }}
    </p>
</div>
