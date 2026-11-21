@extends('layouts.app')

@section('title', 'Services | '.\App\Support\SiteSettings::shortName())
@section('meta_description', 'Explore the professional services offered by '.\App\Support\SiteSettings::legalName().'.')

@section('content')
    <x-page-hero
        eyebrow="Services"
        title="Our services"
        lede="Documented service areas of {{ \App\Support\SiteSettings::legalName() }}. Detailed service descriptions will be confirmed as content is finalised."
    />

    <section>
        <div class="site-container site-section">
            @if ($services->isEmpty())
                <x-empty-state
                    title="Services to be published"
                    text="Active services will appear here once they are confirmed and published in the staff panel."
                />
            @else
                <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $index => $service)
                        <x-service-card
                            :service="$service"
                            @class([
                                'motion-reveal',
                                'motion-reveal-delay-1' => $index % 3 === 1,
                                'motion-reveal-delay-2' => $index % 3 === 2,
                            ])
                        />
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    <x-cta-band />
@endsection
