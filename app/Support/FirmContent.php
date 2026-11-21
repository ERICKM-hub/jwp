<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Support\Str;

class FirmContent
{
    public static function placeholder(string $key): string
    {
        return (string) config("firm.placeholders.{$key}", '[Information to be confirmed.]');
    }

    public static function pendingLabel(): string
    {
        return self::placeholder('pending_label');
    }

    public static function isPlaceholder(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return true;
        }

        $normalized = trim($value);

        foreach (config('firm.placeholders', []) as $placeholder) {
            if (is_string($placeholder) && $normalized === trim($placeholder)) {
                return true;
            }
        }

        if (preg_match('/^\[.*to be confirmed\.?\]$/i', $normalized)) {
            return true;
        }

        return str_contains($normalized, 'to be confirmed');
    }

    public static function firmField(string $field, ?string $placeholderKey = null): string
    {
        $value = SiteSettings::value($field);

        if ($value !== null && $value !== '' && ! self::isPlaceholder($value)) {
            return $value;
        }

        return self::placeholder($placeholderKey ?? $field);
    }

    public static function valueDescription(?string $description): string
    {
        if ($description !== null && $description !== '' && ! self::isPlaceholder($description)) {
            return $description;
        }

        return self::placeholder('value_description');
    }

    public static function formattedAddress(): string
    {
        $formatted = SiteSettings::formattedAddress();

        if ($formatted !== null && $formatted !== '') {
            return $formatted;
        }

        $address = self::firmField('address', 'address');
        $country = SiteSettings::country();

        if ($country && ! str_contains($address, (string) $country)) {
            return $address.', '.$country;
        }

        return $address;
    }

    public static function serviceDescription(?string $description): string
    {
        if ($description !== null && $description !== '' && ! self::isPlaceholder($description)) {
            return $description;
        }

        return self::placeholder('service_description');
    }

    public static function serviceMetaDescription(Service $service): string
    {
        $candidates = [
            $service->seo_description,
            $service->short_description,
            $service->description,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== null && $candidate !== '' && ! self::isPlaceholder($candidate)) {
                return Str::limit(strip_tags($candidate), 160);
            }
        }

        return $service->title.' — documented service area from '.SiteSettings::legalName().'.';
    }

    public static function insightMetaDescription(?string $excerpt, string $title): string
    {
        if ($excerpt !== null && $excerpt !== '' && ! self::isPlaceholder($excerpt)) {
            return Str::limit(strip_tags($excerpt), 160);
        }

        return $title.' — insights from '.SiteSettings::legalName().'.';
    }
}
