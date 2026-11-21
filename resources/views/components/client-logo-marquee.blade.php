@props([
    'clients' => null,
    'headline' => null,
])

@php
    $source = $clients ?? config('firm.clients', []);
    $items = collect($source)
        ->map(function ($client) {
            if (is_string($client)) {
                return ['name' => $client, 'logo' => null];
            }

            $name = $client['display'] ?? $client['name'] ?? '';

            return [
                'name' => $name,
                'logo' => $client['logo'] ?? null,
            ];
        })
        ->filter(fn (array $client) => $client['name'] !== '')
        ->values()
        ->all();

    $headline ??= config('firm.homepage.trust_headline');
@endphp

@if (count($items) > 0)
    <section class="border-y border-brand-navy/8 bg-brand-cream py-8" aria-label="Selected clients">
        <div class="site-container text-center">
            @if (! empty($headline))
                <p class="text-sm text-brand-ink/60">{{ $headline }}</p>
            @endif
        </div>

        <div class="client-marquee mt-5">
            <div class="client-marquee__track">
                @foreach ([1, 2] as $copy)
                    <ul class="client-marquee__group" @if ($copy === 2) aria-hidden="true" @endif>
                        @foreach ($items as $client)
                            <li class="client-marquee__item">
                                @if (! empty($client['logo']))
                                    <img
                                        src="{{ asset($client['logo']) }}"
                                        alt="{{ $client['name'] }}"
                                        class="client-marquee__logo"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                    <span class="sr-only">{{ $client['name'] }}</span>
                                @else
                                    <span class="text-[11px] font-semibold uppercase tracking-wide text-brand-navy/45 sm:text-xs">
                                        {{ $client['name'] }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </section>
@endif
