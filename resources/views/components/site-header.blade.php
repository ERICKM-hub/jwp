@php
    use App\Support\SiteSettings;

    $navItems = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Services', 'route' => 'services.index', 'active' => ['services.index', 'services.show']],
        ['label' => 'Insights', 'route' => 'insights', 'active' => ['insights', 'insights.show']],
        ['label' => 'Team', 'route' => 'team'],
        ['label' => 'Contact', 'route' => 'contact'],
    ];

    $brandLines = config('firm.brand_lines');
@endphp

<header class="sticky top-0 z-50 border-b border-brand-navy/10 bg-brand-white/95 backdrop-blur-sm">
    <div class="site-container flex items-center justify-between gap-3 py-3">
        <a href="{{ route('home') }}" class="inline-flex min-w-0 items-center gap-2.5 text-brand-navy no-underline transition-opacity duration-300 hover:opacity-90 sm:gap-3">
            <x-brand-logo size="sm" :show-fallback-text="false" />
            <span class="hidden min-w-0 flex-col sm:flex">
                <span class="truncate text-sm font-semibold leading-tight tracking-wide">
                    {{ SiteSettings::shortName() }}
                </span>
                @if ($brandLines)
                    <span class="hidden text-[10px] font-medium uppercase tracking-[0.12em] text-brand-ink/55 md:inline">
                        {{ $brandLines }}
                    </span>
                @endif
            </span>
        </a>

        <nav aria-label="Primary" class="hidden items-center gap-5 text-sm xl:gap-6 lg:flex">
            @foreach ($navItems as $item)
                @php
                    $activeRoutes = $item['active'] ?? [$item['route']];
                    $isActive = request()->routeIs(...$activeRoutes);
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'whitespace-nowrap no-underline transition-colors duration-300',
                        'font-semibold text-brand-navy' => $isActive,
                        'text-brand-ink/70 hover:text-brand-navy' => ! $isActive,
                    ])
                    @if ($isActive) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden shrink-0 lg:block">
            <a href="{{ route('contact') }}" class="btn-accent !px-4 !py-2">
                Enquire now
            </a>
        </div>

        <details class="relative shrink-0 lg:hidden">
            <summary
                class="list-none cursor-pointer rounded-lg border border-brand-navy/20 px-3 py-2 text-sm font-medium text-brand-navy transition-colors duration-300 hover:bg-brand-navy/5 [&::-webkit-details-marker]:hidden"
                aria-label="Open menu"
            >
                Menu
            </summary>
            <nav
                aria-label="Mobile"
                class="absolute right-0 z-40 mt-2 w-[min(18rem,calc(100vw-2rem))] rounded-lg border border-brand-navy/10 bg-brand-white py-2 shadow-md"
            >
                @foreach ($navItems as $item)
                    @php
                        $activeRoutes = $item['active'] ?? [$item['route']];
                        $isActive = request()->routeIs(...$activeRoutes);
                    @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        @class([
                            'block px-4 py-2.5 text-sm no-underline transition-colors duration-300',
                            'bg-brand-navy/5 font-semibold text-brand-navy' => $isActive,
                            'text-brand-ink hover:bg-brand-navy/5 hover:text-brand-navy' => ! $isActive,
                        ])
                        @if ($isActive) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <div class="border-t border-brand-navy/10 px-4 py-3">
                    <a href="{{ route('contact') }}" class="btn-accent !flex w-full !px-3.5 !py-2 text-center">
                        Enquire now
                    </a>
                </div>
            </nav>
        </details>
    </div>
</header>
