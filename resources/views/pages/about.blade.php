@extends('layouts.app')

@php
    use App\Support\FirmContent;
    use App\Support\SiteSettings;
@endphp

@section('title', 'About | '.SiteSettings::shortName())
@section('meta_description', SiteSettings::legalName().' — firm overview, guiding principles, and professional approach.')

@section('content')
    <x-page-hero
        eyebrow="About"
        :title="SiteSettings::legalName()"
        :lede="SiteSettings::shortSummary()"
    />

    <section class="border-b border-brand-navy/10 surface">
        <div class="site-container site-section grid gap-6 md:grid-cols-2 md:gap-8">
            <article class="motion-card motion-reveal p-6 sm:p-7">
                <p class="eyebrow">Background</p>
                <h2 class="heading-section mt-2">Our story</h2>
                <div class="mt-3">
                    @if (! empty($ourStory))
                        <p class="leading-relaxed text-brand-ink/80">{{ $ourStory }}</p>
                    @else
                        <x-firm-field field="about" />
                    @endif
                </div>
            </article>

            @if (! empty($ourStory))
                <article class="motion-card motion-reveal motion-reveal-delay-1 p-6 sm:p-7">
                    <p class="eyebrow">Overview</p>
                    <h2 class="heading-section mt-2">Firm introduction</h2>
                    <div class="mt-3">
                        <x-firm-field field="about" />
                    </div>
                </article>
            @endif
        </div>
    </section>

    <section class="border-b border-brand-navy/10">
        <div class="site-container site-section grid gap-6 md:grid-cols-2 md:gap-8">
            <article class="motion-card motion-reveal p-6 sm:p-7">
                <p class="eyebrow">Purpose</p>
                <h2 class="heading-section mt-2">Mission</h2>
                <div class="mt-3">
                    <x-firm-field field="mission" />
                </div>
            </article>
            <article class="motion-card motion-reveal motion-reveal-delay-1 p-6 sm:p-7">
                <p class="eyebrow">Ambition</p>
                <h2 class="heading-section mt-2">Vision</h2>
                <div class="mt-3">
                    <x-firm-field field="vision" />
                </div>
            </article>
        </div>
    </section>

    <section class="border-b border-brand-navy/10 surface">
        <div class="site-container site-section">
            <div class="motion-reveal">
                <x-section-heading
                    title="Core values"
                    lede="The principles that guide our professional practice."
                />
            </div>
            <ul class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($values as $index => $value)
                    <li
                        @class([
                            'min-w-0 border-l-2 border-brand-gold/60 pl-4 motion-reveal',
                            'motion-reveal-delay-1' => $index % 3 === 1,
                            'motion-reveal-delay-2' => $index % 3 === 2,
                        ])
                    >
                        <h3 class="font-semibold text-brand-navy">{{ $value['title'] }}</h3>
                        @php
                            $valueText = FirmContent::valueDescription($value['description'] ?? null);
                            $valueIsPlaceholder = FirmContent::isPlaceholder($valueText);
                        @endphp
                        @if ($valueIsPlaceholder)
                            <x-content-placeholder class="mt-2">
                                {{ $valueText }}
                            </x-content-placeholder>
                        @else
                            <p class="mt-2 text-sm leading-relaxed text-brand-ink/75">{{ $valueText }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="border-b border-brand-navy/10">
        <div class="site-container site-section">
            <div class="motion-reveal max-w-3xl space-y-4">
                <h2 class="heading-section">Our unique value proposition</h2>
                <x-firm-field field="professional_approach" />
            </div>
        </div>
    </section>

    <x-cta-band title="Speak with our team" text="Contact us to discuss your requirements." />
@endsection
