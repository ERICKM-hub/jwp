@props([
    'title' => 'Ready to Transform Your Business?',
    'text' => null,
    'showContactDetails' => false,
    'compact' => false,
])

@php
    use App\Support\FirmContent;
    use App\Support\SiteSettings;

    $text ??= 'Contact '.SiteSettings::shortName().' to arrange a professional consultation.';
    $phone = FirmContent::firmField('phone');
    $email = FirmContent::firmField('email');
    $city = SiteSettings::city();
@endphp

<section {{ $attributes->class(['bg-brand-cta text-brand-white']) }}>
    <div class="site-container flex flex-col gap-6 py-10 sm:py-12 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
        <div class="motion-reveal min-w-0 space-y-3">
            <h2 @class([
                'font-serif tracking-tight text-balance text-brand-white',
                'text-2xl sm:text-3xl' => $compact,
                'text-xl font-semibold sm:text-2xl' => ! $compact,
            ])>{{ $title }}</h2>
            @if ($showContactDetails && $compact)
                <p class="text-sm leading-relaxed text-brand-white/80">
                    {{ $phone }}
                    <span class="mx-2 text-brand-white/40" aria-hidden="true">·</span>
                    {{ $email }}
                    @if ($city)
                        <span class="mx-2 text-brand-white/40" aria-hidden="true">·</span>
                        {{ $city }}, {{ SiteSettings::country() }}
                    @endif
                </p>
            @elseif ($showContactDetails)
                <ul class="space-y-2 text-sm text-brand-white/85">
                    <li>
                        <span class="font-medium text-brand-gold">Phone</span>
                        <span class="ms-2">{{ $phone }}</span>
                    </li>
                    <li>
                        <span class="font-medium text-brand-gold">Email</span>
                        <span class="ms-2 break-all">{{ $email }}</span>
                    </li>
                    <li>
                        <span class="font-medium text-brand-gold">Location</span>
                        <span class="ms-2">{{ FirmContent::formattedAddress() }}</span>
                    </li>
                </ul>
            @else
                <p class="text-sm leading-relaxed text-brand-white/80 sm:text-base">{{ $text }}</p>
            @endif
        </div>
        <a href="{{ route('contact') }}" class="btn-accent w-full shrink-0 sm:w-auto">
            Contact Us
        </a>
    </div>
</section>
