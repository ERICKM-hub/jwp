@extends('layouts.app')

@php
    use App\Support\SiteSettings;

    $pillars = $homepage['service_pillars'] ?? [];
    $firstTestimonial = $testimonials[0] ?? null;
    $quoteText = is_array($firstTestimonial) ? ($firstTestimonial['quote'] ?? '') : (string) ($firstTestimonial ?? '');
    $primaryCta = $homepage['cta_primary_label'] ?? 'Explore Our Services';
    $secondaryCta = $homepage['cta_secondary_label'] ?? 'Contact Us';
@endphp

@section('title', SiteSettings::legalName().' | '.SiteSettings::tagline())
@section('meta_description', SiteSettings::shortSummary() ?: SiteSettings::legalName().' — '.SiteSettings::tagline())

@section('content')
    <div class="bg-brand-cream">
        {{-- Hero — brief identity --}}
        <section class="bg-brand-navy text-brand-white">
            <div class="site-container grid gap-10 py-12 lg:grid-cols-2 lg:items-center lg:py-16">
                <div class="motion-reveal max-w-xl space-y-5">
                    <p class="eyebrow">{{ SiteSettings::tagline() }}</p>
                    <h1 class="font-serif text-3xl leading-tight tracking-tight text-balance sm:text-4xl lg:text-[2.35rem]">
                        {{ SiteSettings::legalName() }}
                    </h1>
                    @if (SiteSettings::shortSummary())
                        <p class="max-w-lg text-sm leading-relaxed text-brand-white/82 sm:text-base">
                            {{ SiteSettings::shortSummary() }}
                        </p>
                    @endif
                    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a href="{{ route('services.index') }}" class="btn-accent w-full sm:w-auto">
                            {{ $primaryCta }}
                        </a>
                        <a href="{{ route('contact') }}" class="btn-hero-outline w-full sm:w-auto">
                            {{ $secondaryCta }}
                        </a>
                    </div>
                </div>
                <div class="motion-reveal motion-reveal-delay-1">
                    <x-home-hero-art />
                </div>
            </div>
        </section>

        {{-- Service pillars --}}
        <section class="site-container site-section">
            <div class="motion-reveal max-w-2xl">
                <p class="eyebrow">{{ $homepage['services_eyebrow'] ?? 'Our services' }}</p>
                <h2 class="mt-2 font-serif text-2xl text-brand-navy sm:text-3xl">
                    {{ $homepage['services_title'] ?? 'Expertise for real progress' }}
                </h2>
            </div>
            <ul class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach ($pillars as $index => $pillar)
                    <li class="motion-reveal min-w-0">
                        <a
                            href="{{ route($pillar['route'], $pillar['slug']) }}"
                            class="home-pillar-card group block h-full no-underline"
                        >
                            <h3 class="font-serif text-lg text-brand-navy group-hover:text-brand-gold">{{ $pillar['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-brand-ink/65">{{ $pillar['description'] }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Process --}}
        @if (count($process) > 0)
            <section class="site-container site-section border-t border-brand-navy/8">
                <div class="motion-reveal max-w-2xl">
                    <p class="eyebrow">{{ $homepage['process_eyebrow'] ?? 'Our approach' }}</p>
                    <h2 class="mt-2 font-serif text-2xl text-brand-navy sm:text-3xl">
                        {{ $homepage['process_title'] ?? 'A clear path to your success' }}
                    </h2>
                </div>
                <ol class="mt-10 grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($process as $step)
                        <li class="motion-reveal relative min-w-0 text-center">
                            <div class="mx-auto mb-3 h-px w-full max-w-[6rem] bg-brand-navy/15 lg:max-w-none"></div>
                            <p class="text-sm font-semibold text-brand-gold">{{ $step['step'] }}</p>
                            <h3 class="mt-2 text-xs font-medium leading-snug text-brand-navy sm:text-sm">{{ $step['title'] }}</h3>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif

        {{-- Brief testimonial --}}
        @if ($quoteText !== '')
            <section class="site-container pb-12 sm:pb-14">
                <blockquote class="motion-reveal rounded-xl bg-brand-gold/12 px-6 py-8 text-center sm:px-10 sm:py-10">
                    <p class="font-serif text-lg italic leading-relaxed text-brand-navy sm:text-xl">
                        “{{ $quoteText }}”
                    </p>
                </blockquote>
            </section>
        @endif
    </div>

    <x-cta-band
        title="Ready to transform your business?"
        :show-contact-details="true"
        compact
    />
@endsection
