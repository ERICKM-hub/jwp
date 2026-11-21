@extends('layouts.app')

@php
    use App\Support\FirmContent;

    $overview = FirmContent::serviceDescription($service->description);
    $overviewIsPlaceholder = FirmContent::isPlaceholder($overview);
    $lede = FirmContent::serviceDescription($service->short_description);
@endphp

@section('title', $service->pageTitle())
@section('meta_description', $service->pageDescription())

@section('content')
    <x-page-hero
        eyebrow="Services"
        :title="$service->title"
        :lede="$lede"
    />

    <section class="border-b border-brand-navy/10">
        <div class="site-prose site-section">
            <div class="motion-reveal space-y-4">
                <h2 class="heading-section">Overview</h2>
                @if ($overviewIsPlaceholder)
                    <x-content-placeholder :label="FirmContent::pendingLabel()">
                        {{ $overview }}
                    </x-content-placeholder>
                @else
                    <p class="leading-relaxed whitespace-pre-line text-brand-ink/80">
                        {{ $overview }}
                    </p>
                @endif
                @if ($overviewIsPlaceholder)
                    <p class="text-sm text-brand-ink/60">
                        Full service narrative will be published once confirmed by {{ \App\Support\SiteSettings::legalName() }}.
                    </p>
                @endif
            </div>
        </div>
    </section>

    @if ($relatedServices->isNotEmpty())
        <section class="border-b border-brand-navy/10 surface">
            <div class="site-container site-section">
                <div class="motion-reveal">
                    <x-section-heading title="Related services" />
                </div>
                <ul class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedServices as $index => $related)
                        <x-service-card
                            :service="$related"
                            @class([
                                'motion-reveal',
                                'motion-reveal-delay-1' => $index === 1,
                                'motion-reveal-delay-2' => $index === 2,
                            ])
                        />
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-cta-band
        title="Discuss this service"
        text="Contact us to enquire about {{ $service->title }}."
    />
@endsection
