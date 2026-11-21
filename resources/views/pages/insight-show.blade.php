@extends('layouts.app')

@php
    use App\Support\FirmContent;
@endphp

@section('title', $insight->pageTitle())
@section('meta_description', $insight->pageDescription())

@section('content')
    <x-page-hero
        eyebrow="Insights"
        :title="$insight->title"
        :lede="$insight->excerpt"
    />

    <article class="border-b border-brand-navy/10">
        <div class="site-prose site-section">
            <div class="motion-reveal">
                <div class="mb-6 flex flex-wrap items-baseline gap-x-3 gap-y-1 meta">
                    @if ($insight->published_at)
                        <time datetime="{{ $insight->published_at->toDateString() }}">
                            {{ $insight->published_at->toFormattedDateString() }}
                        </time>
                    @endif

                    @if ($insight->publishedAuthorName())
                        <span aria-hidden="true">·</span>
                        <span class="normal-case tracking-normal text-brand-ink/65">{{ $insight->publishedAuthorName() }}</span>
                    @endif
                </div>

                @if ($insight->featuredImageUrl())
                    <div class="motion-image mb-8 aspect-[16/9] bg-brand-navy/5">
                        <img
                            src="{{ $insight->featuredImageUrl() }}"
                            alt="{{ $insight->featuredImageAlt() }}"
                            loading="lazy"
                        >
                    </div>
                @endif

                @php
                    $body = $insight->body ?: FirmContent::placeholder('insight_body');
                    $bodyIsPlaceholder = FirmContent::isPlaceholder($body);
                @endphp

                @if ($bodyIsPlaceholder)
                    <x-content-placeholder :label="FirmContent::pendingLabel()">
                        {{ $body }}
                    </x-content-placeholder>
                @else
                    <div class="max-w-none text-base leading-relaxed whitespace-pre-line text-brand-ink/85">
                        {{ $body }}
                    </div>
                @endif

                <p class="mt-10">
                    <a href="{{ route('insights') }}" class="link-quiet text-sm">
                        Back to insights
                    </a>
                </p>
            </div>
        </div>
    </article>

    <x-cta-band />
@endsection
