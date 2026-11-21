@props([
    'field',
    'placeholderKey' => null,
    'value' => null,
])

@php
    use App\Support\FirmContent;
    use App\Support\SiteSettings;

    $resolved = $value ?? SiteSettings::value($field);
    $display = ($resolved !== null && $resolved !== '' && ! FirmContent::isPlaceholder((string) $resolved))
        ? (string) $resolved
        : FirmContent::placeholder($placeholderKey ?? $field);
    $isPlaceholder = FirmContent::isPlaceholder($display);
@endphp

@if ($isPlaceholder)
    <x-content-placeholder {{ $attributes }}>
        {{ $display }}
    </x-content-placeholder>
@else
    <p {{ $attributes->class(['leading-relaxed text-brand-ink/80']) }}>
        {{ $display }}
    </p>
@endif
