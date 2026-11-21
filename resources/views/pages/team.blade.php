@extends('layouts.app')

@php
    use App\Support\SiteSettings;
@endphp

@section('title', 'Team | '.SiteSettings::shortName())
@section('meta_description', 'Published team profiles from '.SiteSettings::legalName().'.')

@section('content')
    <x-page-hero
        eyebrow="Team"
        title="Our people"
        lede="Profiles of the professionals behind {{ SiteSettings::legalName() }} appear here when published by the firm."
    />

    <section>
        <div class="site-container site-section">
            @if ($teamMembers->isEmpty())
                <x-empty-state
                    title="No published team profiles yet"
                    :action-href="route('contact')"
                    action-label="Contact the firm"
                >
                    Individual biographies are managed in the staff panel and shown only when published. No placeholder people or stock profiles are displayed.
                </x-empty-state>
            @else
                <ul class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($teamMembers as $index => $member)
                        <x-team-card
                            :member="$member"
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
