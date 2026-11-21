<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        if (isset($data['short_description']) && is_string($data['short_description'])) {
            $data['short_description'] = strip_tags($data['short_description']);
        }

        if (isset($data['description']) && is_string($data['description'])) {
            $data['description'] = strip_tags($data['description']);
        }

        if (isset($data['seo_title']) && is_string($data['seo_title'])) {
            $data['seo_title'] = strip_tags($data['seo_title']);
        }

        if (isset($data['seo_description']) && is_string($data['seo_description'])) {
            $data['seo_description'] = strip_tags($data['seo_description']);
        }

        return $data;
    }
}
