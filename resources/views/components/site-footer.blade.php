@php
    use App\Support\SiteSettings;

    $year = config('firm.copyright_year') ?: now()->year;
    $socialLinks = SiteSettings::socialLinks();
    $whatsappUrl = SiteSettings::whatsappUrl();
    $email = SiteSettings::email();
    $phone = SiteSettings::phone();
@endphp

<footer class="bg-brand-navy text-brand-white">
    <div class="site-container py-10 sm:py-12">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-[1.2fr_1fr_auto] lg:items-start lg:gap-10">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 no-underline">
                    <img
                        src="{{ SiteSettings::logoUrl() }}"
                        alt="{{ SiteSettings::legalName() }} logo"
                        width="48"
                        height="36"
                        class="h-9 w-auto rounded bg-brand-white/95 p-1"
                    >
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold tracking-wide text-brand-white">
                            {{ SiteSettings::shortName() }}
                        </span>
                        <span class="mt-0.5 block text-[11px] leading-snug text-brand-white/55">
                            {{ SiteSettings::tagline() }}
                        </span>
                    </span>
                </a>
                @if ($email || $phone)
                    <ul class="mt-4 space-y-1 text-sm text-brand-white/70">
                        @if ($phone)
                            <li>{{ $phone }}</li>
                        @endif
                        @if ($email)
                            <li class="break-all">{{ $email }}</li>
                        @endif
                        @if (SiteSettings::formattedAddress())
                            <li>{{ SiteSettings::formattedAddress() }}</li>
                        @endif
                    </ul>
                @endif
            </div>

            <nav aria-label="Footer" class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm sm:justify-items-start">
                <a href="{{ route('home') }}" class="text-brand-white/80 no-underline hover:text-brand-white">Home</a>
                <a href="{{ route('about') }}" class="text-brand-white/80 no-underline hover:text-brand-white">About</a>
                <a href="{{ route('services.index') }}" class="text-brand-white/80 no-underline hover:text-brand-white">Services</a>
                <a href="{{ route('insights') }}" class="text-brand-white/80 no-underline hover:text-brand-white">Insights</a>
                <a href="{{ route('team') }}" class="text-brand-white/80 no-underline hover:text-brand-white">Team</a>
                <a href="{{ route('contact') }}" class="text-brand-white/80 no-underline hover:text-brand-white">Contact</a>
            </nav>

            @if ($socialLinks !== [] || $whatsappUrl)
                <ul class="flex flex-wrap items-center gap-2.5">
                    @foreach ($socialLinks as $network => $url)
                        <li>
                            <a
                                href="{{ $url }}"
                                class="inline-flex h-9 items-center rounded-lg bg-brand-white/10 px-3 text-xs font-semibold text-brand-white no-underline transition-colors hover:bg-brand-gold hover:text-brand-navy"
                                rel="noopener noreferrer"
                                target="_blank"
                            >
                                {{ $network === 'x' ? 'X' : ucfirst($network) }}
                            </a>
                        </li>
                    @endforeach
                    @if ($whatsappUrl)
                        <li>
                            <a
                                href="{{ $whatsappUrl }}"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-whatsapp text-brand-white no-underline transition-colors hover:bg-brand-whatsapp-hover"
                                rel="noopener noreferrer"
                                target="_blank"
                                aria-label="WhatsApp"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </a>
                        </li>
                    @endif
                </ul>
            @endif
        </div>

        <div class="mt-8 border-t border-brand-white/10 pt-6 text-xs text-brand-white/55">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ $year }} {{ SiteSettings::legalName() }}. All rights reserved.</p>
                <a href="{{ route('privacy') }}" class="no-underline hover:text-brand-white">Privacy Policy</a>
            </div>
        </div>
    </div>
</footer>
