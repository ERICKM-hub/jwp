<?php

namespace App\Filament\Resources\Insights\Pages;

use App\Filament\Resources\Insights\InsightResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateInsight extends CreateRecord
{
    protected static string $resource = InsightResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        foreach (['excerpt', 'body', 'author_name', 'seo_title', 'seo_description'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = strip_tags(trim($data[$field]));
            }
        }

        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        return $data;
    }
}
