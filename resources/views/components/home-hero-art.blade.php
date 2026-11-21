@php
    use App\Support\SiteSettings;

    $script = config('firm.homepage.hero_image_script', 'Ideas · Strategy · Results');
@endphp

<div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-[#2a3f5f] ring-1 ring-brand-white/10">
    <img
        src="{{ SiteSettings::heroImageUrl() }}"
        alt=""
        width="800"
        height="600"
        class="absolute inset-0 h-full w-full object-cover opacity-25"
        fetchpriority="high"
        aria-hidden="true"
    >
    @if ($script)
        <p class="relative z-10 px-5 pt-5 font-serif text-sm italic text-brand-gold sm:text-base">
            {{ $script }}
        </p>
    @endif
    <div class="absolute inset-x-0 bottom-0 flex h-[45%] items-end gap-2 px-6 pb-6" aria-hidden="true">
        <span class="h-16 w-[38%] bg-[#5c4a3d]"></span>
        <span class="mb-2 h-3 flex-1 bg-brand-gold"></span>
        <span class="h-20 w-[28%] skew-x-[-12deg] bg-brand-white/90"></span>
    </div>
</div>
