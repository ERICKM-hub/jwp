<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Str;

class SiteSettings
{
    /**
     * Known setting keys managed by the staff settings form.
     *
     * @return list<string>
     */
    public static function keys(): array
    {
        return [
            'firm.legal_name',
            'firm.short_name',
            'firm.tagline',
            'firm.country',
            'firm.email',
            'firm.phone',
            'firm.address',
            'firm.city',
            'firm.business_hours',
            'firm.website',
            'firm.short_summary',
            'firm.about',
            'firm.mission',
            'firm.vision',
            'firm.professional_approach',
            'social.facebook',
            'social.linkedin',
            'social.instagram',
            'social.x',
            'branding.logo_path',
            'branding.hero_image_path',
        ];
    }

    /**
     * Raw DB value (null if unset). Does not fall back to config.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }

    /**
     * Firm field: DB value first, then config('firm.*') fallback for firm.* keys, else null.
     */
    public static function value(string $field): ?string
    {
        $dbKey = self::normalizeKey($field);
        $settings = SiteSetting::allCached();

        if (array_key_exists($dbKey, $settings) && $settings[$dbKey] !== null && $settings[$dbKey] !== '') {
            $candidate = (string) $settings[$dbKey];

            if (! FirmContent::isPlaceholder($candidate)) {
                return $candidate;
            }
        }

        if (str_starts_with($dbKey, 'firm.')) {
            $configValue = config('firm.'.substr($dbKey, 5));

            if (is_string($configValue) && $configValue !== '' && ! FirmContent::isPlaceholder($configValue)) {
                return $configValue;
            }
        }

        return null;
    }

    public static function normalizeKey(string $field): string
    {
        if (str_starts_with($field, 'firm.') || str_starts_with($field, 'social.') || str_starts_with($field, 'branding.')) {
            return $field;
        }

        return 'firm.'.$field;
    }

    public static function legalName(): string
    {
        return self::value('legal_name') ?? 'JW Pencraft Solutions Limited';
    }

    public static function shortName(): string
    {
        return self::value('short_name') ?? 'JW Pencraft Solutions';
    }

    public static function tagline(): string
    {
        return self::value('tagline') ?? 'Seamless Solutions, Boundless Success.';
    }

    public static function shortSummary(): ?string
    {
        return self::value('short_summary');
    }

    public static function about(): ?string
    {
        return self::value('about');
    }

    public static function mission(): ?string
    {
        return self::value('mission');
    }

    public static function vision(): ?string
    {
        return self::value('vision');
    }

    public static function professionalApproach(): ?string
    {
        return self::value('professional_approach');
    }

    public static function email(): ?string
    {
        return self::value('email');
    }

    public static function phone(): ?string
    {
        return self::value('phone');
    }

    public static function whatsappUrl(): ?string
    {
        $raw = config('firm.whatsapp') ?: self::phone();

        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw);

        if (! is_string($digits) || $digits === '') {
            return null;
        }

        return 'https://wa.me/'.$digits;
    }

    public static function address(): ?string
    {
        return self::value('address');
    }

    public static function city(): ?string
    {
        return self::value('city');
    }

    public static function country(): ?string
    {
        return self::value('country') ?? 'Kenya';
    }

    public static function businessHours(): ?string
    {
        return self::value('business_hours');
    }

    public static function website(): ?string
    {
        return self::sanitizeUrl(self::value('website'));
    }

    /**
     * @return array<string, string>
     */
    public static function socialLinks(): array
    {
        $links = [];

        foreach (['facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'x' => 'X'] as $network => $label) {
            $url = self::sanitizeUrl(self::get('social.'.$network));

            if ($url !== null) {
                $links[$network] = $url;
            }
        }

        return $links;
    }

    public static function logoPath(): string
    {
        $path = self::get('branding.logo_path');

        if (is_string($path) && $path !== '') {
            return $path;
        }

        return 'images/jw-pencraft-logo.png';
    }

    public static function heroImagePath(): string
    {
        $path = self::get('branding.hero_image_path');

        if (is_string($path) && $path !== '') {
            return $path;
        }

        return 'images/hero.png';
    }

    public static function logoUrl(): string
    {
        return asset(ltrim(self::logoPath(), '/'));
    }

    public static function heroImageUrl(): string
    {
        return asset(ltrim(self::heroImagePath(), '/'));
    }

    public static function formattedAddress(): ?string
    {
        $line = self::address();

        if ($line === null || $line === '') {
            return null;
        }

        return collect([$line, self::city(), self::country()])->filter()->implode(', ');
    }

    public static function sanitizeUrl(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (! Str::startsWith($url, ['http://', 'https://'])) {
            return null;
        }

        return $url;
    }

    /**
     * @return array<string, string|null>
     */
    public static function formState(): array
    {
        $state = [];

        foreach (self::keys() as $key) {
            $state[$key] = self::get($key);
        }

        return $state;
    }
}
