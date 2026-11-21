@props([
    'service',
    'variant' => 'default',
])

@php
    use App\Support\FirmContent;

    $description = FirmContent::serviceDescription($service->short_description);
    $isPlaceholder = FirmContent::isPlaceholder($description);
    $icon = $service->icon ?: 'corporate';
@endphp

<li {{ $attributes->class(['h-full min-w-0']) }}>
    <a
        href="{{ route('services.show', $service) }}"
        class="motion-card group flex h-full min-w-0 flex-col p-5 no-underline outline-offset-4"
    >
        <span
            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-navy/5 text-brand-navy"
            aria-hidden="true"
        >
            <x-service-icon :name="$icon" class="h-5 w-5" />
        </span>

        <h3 class="mt-4 text-base font-semibold leading-snug text-balance text-brand-navy transition-colors duration-300 group-hover:text-brand-gold sm:text-[1.05rem]">
            {{ $service->title }}
        </h3>

        <p @class([
            'mt-2 flex-1 text-sm leading-relaxed text-brand-ink/70',
            'italic' => $isPlaceholder,
        ])>
            {{ $description }}
        </p>

        <span class="mt-auto inline-flex items-center gap-1 pt-4 text-sm font-semibold text-brand-navy transition-colors duration-300 group-hover:text-brand-gold">
            Explore service
            <span aria-hidden="true">→</span>
        </span>
    </a>
</li>
