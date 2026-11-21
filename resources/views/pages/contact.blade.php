@extends('layouts.app')

@php
    use App\Support\FirmContent;
    use App\Support\SiteSettings;

    $email = SiteSettings::email();
    $phone = SiteSettings::phone();
    $hours = SiteSettings::businessHours();
    $website = SiteSettings::website();
    $socialLinks = SiteSettings::socialLinks();
    $addressDisplay = FirmContent::formattedAddress();
@endphp

@section('title', 'Contact | '.SiteSettings::shortName())
@section('meta_description', 'Contact '.SiteSettings::legalName().' — enquiry details and office information when confirmed.')

@section('content')
    <x-page-hero
        eyebrow="Contact"
        title="Get in touch"
        lede="Send an enquiry to {{ SiteSettings::legalName() }}. Enquiries are stored for review by our team. Email delivery is not enabled yet."
    />

    <section class="border-b border-brand-navy/10">
        <div class="site-container site-section grid gap-10 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:gap-12">
            <aside class="motion-reveal min-w-0 space-y-6">
                <div>
                    <h2 class="heading-section">Contact details</h2>
                    <p class="mt-2 text-sm text-brand-ink/70">
                        Items shown with dashed borders are {{ strtolower(FirmContent::pendingLabel()) }}.
                    </p>
                </div>

                <dl class="space-y-5 text-sm">
                    <div>
                        <dt class="font-semibold text-brand-navy">Email</dt>
                        <dd class="mt-1.5 break-words">
                            @if ($email === null)
                                <x-content-placeholder>{{ FirmContent::firmField('email') }}</x-content-placeholder>
                            @else
                                <a href="mailto:{{ $email }}" class="text-brand-ink/80">{{ $email }}</a>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-brand-navy">Phone</dt>
                        <dd class="mt-1.5">
                            @if ($phone === null)
                                <x-content-placeholder>{{ FirmContent::firmField('phone') }}</x-content-placeholder>
                            @else
                                <span class="text-brand-ink/80">{{ $phone }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-brand-navy">Office</dt>
                        <dd class="mt-1.5">
                            @if (SiteSettings::address() === null)
                                <x-content-placeholder>{{ $addressDisplay }}</x-content-placeholder>
                            @else
                                <span class="text-brand-ink/80">{{ $addressDisplay }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-brand-navy">Hours</dt>
                        <dd class="mt-1.5">
                            @if ($hours === null)
                                <x-content-placeholder>{{ FirmContent::firmField('business_hours') }}</x-content-placeholder>
                            @else
                                <span class="text-brand-ink/80">{{ $hours }}</span>
                            @endif
                        </dd>
                    </div>
                    @if ($website)
                        <div>
                            <dt class="font-semibold text-brand-navy">Website</dt>
                            <dd class="mt-1.5 break-all">
                                <a href="{{ $website }}" class="link-quiet" rel="noopener noreferrer" target="_blank">{{ $website }}</a>
                            </dd>
                        </div>
                    @endif
                    @if ($socialLinks !== [])
                        <div>
                            <dt class="font-semibold text-brand-navy">Social</dt>
                            <dd class="mt-1.5 flex flex-wrap gap-3">
                                @foreach ($socialLinks as $network => $url)
                                    <a href="{{ $url }}" class="link-quiet" rel="noopener noreferrer" target="_blank">
                                        {{ $network === 'x' ? 'X' : ucfirst($network) }}
                                    </a>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>

                <div class="border-t border-brand-navy/10 pt-5">
                    <h3 class="text-sm font-semibold text-brand-navy">Office / location</h3>
                    <p class="mt-2 text-sm leading-relaxed text-brand-ink/70">
                        A map may be added later. Current office details: {{ FirmContent::formattedAddress() }}.
                    </p>
                </div>
            </aside>

            <div class="motion-reveal motion-reveal-delay-1 min-w-0">
                <h2 class="heading-section">Enquiry form</h2>
                <p class="mt-2 text-sm leading-relaxed text-brand-ink/70">
                    Please do not include confidential legal case details, national ID numbers, payment information, or other sensitive personal documents in this form.
                </p>

                @if (session('enquiry_success'))
                    <div
                        class="mt-4 rounded-lg border border-brand-navy/15 bg-brand-navy/[0.04] px-4 py-3 text-sm text-brand-navy"
                        role="status"
                    >
                        {{ session('enquiry_success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mt-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800"
                        role="alert"
                    >
                        <p class="font-semibold">Please correct the highlighted fields.</p>
                        <ul class="mt-2 list-disc space-y-1 ps-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    class="mt-6 space-y-4"
                    action="{{ route('contact.submit') }}"
                    method="post"
                    aria-describedby="enquiry-note"
                >
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-brand-navy">
                            Full name <span class="text-brand-gold" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            autocomplete="name"
                            required
                            maxlength="120"
                            value="{{ old('name') }}"
                            @class([
                                'form-control',
                                'form-control-error' => $errors->has('name'),
                            ])
                            @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                        >
                        @error('name')
                            <p id="name-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-brand-navy">
                            Email <span class="text-brand-gold" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            maxlength="255"
                            value="{{ old('email') }}"
                            @class([
                                'form-control',
                                'form-control-error' => $errors->has('email'),
                            ])
                            @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                        >
                        @error('email')
                            <p id="email-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="min-w-0">
                            <label for="phone" class="block text-sm font-medium text-brand-navy">
                                Phone <span class="font-normal text-brand-ink/50">(optional)</span>
                            </label>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                maxlength="40"
                                value="{{ old('phone') }}"
                                @class([
                                    'form-control',
                                    'form-control-error' => $errors->has('phone'),
                                ])
                            >
                            @error('phone')
                                <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="min-w-0">
                            <label for="organization" class="block text-sm font-medium text-brand-navy">
                                Organization <span class="font-normal text-brand-ink/50">(optional)</span>
                            </label>
                            <input
                                id="organization"
                                name="organization"
                                type="text"
                                autocomplete="organization"
                                maxlength="160"
                                value="{{ old('organization') }}"
                                @class([
                                    'form-control',
                                    'form-control-error' => $errors->has('organization'),
                                ])
                            >
                            @error('organization')
                                <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-brand-navy">
                            Subject <span class="font-normal text-brand-ink/50">(optional)</span>
                        </label>
                        <input
                            id="subject"
                            name="subject"
                            type="text"
                            maxlength="160"
                            value="{{ old('subject') }}"
                            @class([
                                'form-control',
                                'form-control-error' => $errors->has('subject'),
                            ])
                        >
                        @error('subject')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-brand-navy">
                            Message <span class="text-brand-gold" aria-hidden="true">*</span>
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            required
                            maxlength="5000"
                            @class([
                                'form-control min-h-[9rem]',
                                'form-control-error' => $errors->has('message'),
                            ])
                            @if ($errors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p id="message-error" class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <p id="enquiry-note" class="text-xs leading-relaxed text-brand-ink/60">
                        Required fields are marked with an asterisk. Submitting this form stores your enquiry for administrative follow-up. It does not send an email automatically.
                    </p>

                    <button type="submit" class="btn-primary w-full sm:w-auto">
                        Submit enquiry
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
