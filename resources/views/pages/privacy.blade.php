@extends('layouts.app')

@php
    use App\Support\SiteSettings;
@endphp

@section('title', 'Privacy Policy | '.SiteSettings::shortName())
@section('meta_description', 'Privacy policy for '.SiteSettings::legalName().'.')

@section('content')
    <x-page-hero
        eyebrow="Legal"
        title="Privacy Policy"
        lede="This page sets out a basic privacy framework for visitors to the {{ SiteSettings::shortName() }} website. Specific processing details will be confirmed as systems and practices are finalised."
    />

    <section>
        <div class="site-prose site-section space-y-8 text-brand-ink/85">
            <article class="space-y-3">
                <h2 class="heading-section">1. Who we are</h2>
                <p class="leading-relaxed">
                    {{ SiteSettings::legalName() }} operates this website. Contact details for privacy enquiries will be published once confirmed. Until then, please use the details listed on the
                    <a href="{{ route('contact') }}" class="font-medium text-brand-navy underline-offset-2 hover:underline">contact page</a>
                    once they are available.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="heading-section">2. Information we may collect</h2>
                <p class="leading-relaxed">
                    Depending on how you use the site, we may collect information you voluntarily provide through the contact enquiry form (such as name, email address, phone number, organisation, subject, and message), and limited technical information such as browser type, pages visited, and approximate location derived from IP address, where applicable.
                </p>
                <p class="text-sm italic text-brand-ink/65">
                    [Specific data categories and collection methods to be confirmed.]
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="heading-section">3. How we use information</h2>
                <p class="leading-relaxed">
                    Contact enquiries submitted through this website may be stored in our systems for communication and administrative purposes, including reviewing and responding to your message. Personal information may also be used to improve the website, meet legal obligations, and operate the firm’s professional services where relevant.
                </p>
                <p class="text-sm italic text-brand-ink/65">
                    [Purposes and lawful bases under applicable Kenyan data-protection law to be confirmed by the firm.]
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="heading-section">4. Sharing of information</h2>
                <p class="leading-relaxed">
                    We do not sell personal information. Information may be shared with service providers who support website hosting or operations, or where disclosure is required by law.
                </p>
                <p class="text-sm italic text-brand-ink/65">
                    [Named processors and subprocessors to be confirmed — none are asserted here.]
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="heading-section">5. Retention</h2>
                <p class="leading-relaxed">
                    Information will be retained only for as long as needed for the purposes for which it was collected, or as required by law.
                </p>
                <p class="text-sm italic text-brand-ink/65">
                    [Retention periods to be confirmed.]
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="heading-section">6. Your rights</h2>
                <p class="leading-relaxed">
                    Depending on applicable law in Kenya, you may have rights to access, correct, or request deletion of personal information we hold about you, and to raise concerns with the relevant regulatory authority.
                </p>
                <p class="text-sm italic text-brand-ink/65">
                    [Rights request process and contact channel to be confirmed.]
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="heading-section">7. Updates</h2>
                <p class="leading-relaxed">
                    This policy may be updated from time to time. The version published on this website is the current version.
                </p>
                <p class="text-sm text-brand-ink/65">
                    Page structure only — substantive privacy review by the firm is still pending. No last-reviewed date is asserted here.
                </p>
            </article>
        </div>
    </section>
@endsection
