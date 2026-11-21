<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceCatalogue
{
    /**
     * @return Collection<int, array{title: string, slug: string, short_description: string, description: string, icon: string, featured: bool}>
     */
    public static function all(): Collection
    {
        return collect(config('services_catalogue', []))
            ->map(fn (array $service): array => self::normalize($service))
            ->values();
    }

    /**
     * @return Collection<int, array{title: string, slug: string, short_description: string, description: string, icon: string, featured: bool}>
     */
    public static function featured(): Collection
    {
        return self::all()->where('featured', true)->values();
    }

    /**
     * @return array{title: string, slug: string, short_description: string, description: string, icon: string, featured: bool}|null
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::all()->firstWhere('slug', $slug);
    }

    /**
     * @return Collection<int, array{title: string, slug: string, short_description: string, description: string, icon: string, featured: bool}>
     */
    public static function related(string $slug, int $limit = 3): Collection
    {
        return self::all()
            ->reject(fn (array $service): bool => $service['slug'] === $slug)
            ->take($limit)
            ->values();
    }

    /**
     * @param  array<string, mixed>  $service
     * @return array{title: string, slug: string, short_description: string, description: string, icon: string, featured: bool}
     */
    protected static function normalize(array $service): array
    {
        $title = (string) ($service['title'] ?? '');

        return [
            'title' => $title,
            'slug' => (string) ($service['slug'] ?? Str::slug($title)),
            'short_description' => (string) ($service['short_description'] ?? FirmContent::placeholder('service_description')),
            'description' => (string) ($service['description'] ?? FirmContent::placeholder('service_description')),
            'icon' => (string) ($service['icon'] ?? 'default'),
            'featured' => (bool) ($service['featured'] ?? false),
        ];
    }
}
