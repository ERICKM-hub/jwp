<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Support\FirmContent;
use Illuminate\Database\Seeder;

class ServiceCatalogueSeeder extends Seeder
{
    /**
     * Seed the confirmed service catalogue from config.
     *
     * Idempotent by slug. Creates missing services with brief-approved copy.
     * On re-run, updates catalogue identity fields and fills placeholder
     * descriptions from the brief, without overwriting administrator edits.
     */
    public function run(): void
    {
        $catalogue = config('services_catalogue', []);

        foreach (array_values($catalogue) as $index => $item) {
            $slug = $item['slug'] ?? null;

            if (! is_string($slug) || $slug === '') {
                continue;
            }

            $existing = Service::query()->where('slug', $slug)->first();

            if ($existing) {
                $updates = [
                    'title' => (string) ($item['title'] ?? $existing->title),
                    'icon' => $item['icon'] ?? $existing->icon,
                    'sort_order' => $index,
                ];

                foreach (['short_description', 'description'] as $field) {
                    $current = $existing->{$field};
                    $incoming = $item[$field] ?? null;

                    if (
                        is_string($incoming)
                        && $incoming !== ''
                        && ! FirmContent::isPlaceholder($incoming)
                        && (blank($current) || FirmContent::isPlaceholder((string) $current))
                    ) {
                        $updates[$field] = $incoming;
                    }
                }

                $existing->forceFill($updates)->save();

                continue;
            }

            Service::query()->create([
                'title' => (string) ($item['title'] ?? $slug),
                'slug' => $slug,
                'short_description' => $item['short_description'] ?? null,
                'description' => $item['description'] ?? null,
                'icon' => $item['icon'] ?? null,
                'featured' => (bool) ($item['featured'] ?? false),
                'is_active' => true,
                'sort_order' => $index,
                'seo_title' => null,
                'seo_description' => null,
            ]);
        }
    }
}
