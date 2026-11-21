@props([
    'member',
])

@php
    $photoUrl = $member->photoUrl();
    $email = $member->publishedEmail();
    $phone = $member->publishedPhone();
    $linkedin = $member->publishedLinkedInUrl();
@endphp

<li {{ $attributes->class(['h-full min-w-0']) }}>
    <article class="motion-card group flex h-full min-w-0 flex-col overflow-hidden">
        @if ($photoUrl)
            <div class="motion-image aspect-[4/3] bg-brand-navy/5">
                <img
                    src="{{ $photoUrl }}"
                    alt="{{ $member->photoAlt() }}"
                    loading="lazy"
                >
            </div>
        @else
            <div class="flex aspect-[4/3] items-center justify-center bg-brand-navy/5" aria-hidden="true">
                <span class="text-2xl font-semibold tracking-wide text-brand-navy/30">
                    {{ $member->initials() }}
                </span>
            </div>
        @endif

        <div class="flex min-w-0 flex-1 flex-col p-5">
            <h2 class="text-base font-semibold leading-snug text-balance text-brand-navy sm:text-lg">
                {{ $member->name }}
            </h2>

            @if ($member->position)
                <p class="mt-1 text-sm leading-snug text-brand-gold">{{ $member->position }}</p>
            @endif

            @if ($member->qualification)
                <p class="mt-1 text-sm text-brand-ink/65">{{ $member->qualification }}</p>
            @endif

            @if ($member->bio)
                <p class="mt-3 flex-1 text-sm leading-relaxed text-brand-ink/80">{{ $member->bio }}</p>
            @endif

            @if ($email || $phone || $linkedin)
                <ul class="mt-auto space-y-1 break-words pt-4 text-sm">
                    @if ($email)
                        <li>
                            <a href="mailto:{{ $email }}" class="link-quiet">
                                {{ $email }}
                            </a>
                        </li>
                    @endif
                    @if ($phone)
                        <li class="text-brand-ink/75">{{ $phone }}</li>
                    @endif
                    @if ($linkedin)
                        <li>
                            <a
                                href="{{ $linkedin }}"
                                class="link-quiet"
                                rel="noopener noreferrer"
                                target="_blank"
                            >
                                LinkedIn profile
                            </a>
                        </li>
                    @endif
                </ul>
            @endif
        </div>
    </article>
</li>
