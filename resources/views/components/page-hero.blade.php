@props([
    'eyebrow' => null,
    'title',
    'lede' => null,
])

<section {{ $attributes->class(['border-b border-brand-navy/10 bg-brand-navy text-brand-white']) }}>
    <div class="site-container site-section-sm">
        <div class="motion-reveal max-w-2xl">
            @if ($eyebrow)
                <p class="eyebrow">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-balance sm:text-3xl lg:text-4xl">
                {{ $title }}
            </h1>
            @if ($lede)
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-brand-white/80 sm:text-base">
                    {{ $lede }}
                </p>
            @endif
        </div>
    </div>
</section>
