@props([
    'insight',
    'variant' => 'default',
])

<li {{ $attributes->class(['h-full min-w-0']) }}>
    <a
        href="{{ route('insights.show', $insight) }}"
        class="motion-card group flex h-full min-w-0 flex-col overflow-hidden no-underline outline-offset-4"
    >
        @if ($insight->featuredImageUrl())
            <div class="motion-image aspect-[16/10] bg-brand-navy/5">
                <img
                    src="{{ $insight->featuredImageUrl() }}"
                    alt="{{ $insight->featuredImageAlt() }}"
                    loading="lazy"
                >
            </div>
        @endif

        <div class="flex min-w-0 flex-1 flex-col p-5">
            @if ($insight->published_at)
                <p class="meta">
                    <time datetime="{{ $insight->published_at->toDateString() }}">
                        {{ $insight->published_at->toFormattedDateString() }}
                    </time>
                </p>
            @endif

            <h2 class="mt-2 text-base font-semibold leading-snug text-balance text-brand-navy transition-colors duration-300 group-hover:text-brand-gold sm:text-lg">
                {{ $insight->title }}
            </h2>

            @if ($insight->excerpt)
                <p class="mt-2 flex-1 text-sm leading-relaxed text-brand-ink/75">
                    {{ $insight->excerpt }}
                </p>
            @endif

            <span class="mt-auto inline-flex pt-4 text-sm font-semibold text-brand-navy transition-colors duration-300 group-hover:text-brand-gold">
                Read more
                <span class="ms-1" aria-hidden="true">→</span>
            </span>
        </div>
    </a>
</li>
