@extends('layouts.app')

@section('title', 'Insights | '.\App\Support\SiteSettings::shortName())
@section('meta_description', 'Articles and professional updates from '.\App\Support\SiteSettings::legalName().' when published.')

@section('content')
    <x-page-hero
        eyebrow="Insights"
        title="Articles & updates"
        lede="Professional commentary and firm updates will be published here once confirmed."
    />

    <section>
        <div class="site-container site-section">
            @if ($insights->isEmpty())
                <x-empty-state title="No published insights yet">
                    This space will host confirmed insights, news, and professional updates from {{ \App\Support\SiteSettings::legalName() }}. Articles are added through the staff panel; none are shown until published.
                </x-empty-state>
            @else
                <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($insights as $index => $insight)
                        <x-insight-card
                            :insight="$insight"
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
@endsection
