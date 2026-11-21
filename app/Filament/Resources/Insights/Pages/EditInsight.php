<?php

namespace App\Filament\Resources\Insights\Pages;

use App\Filament\Resources\Insights\InsightResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInsight extends EditRecord
{
    protected static string $resource = InsightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach (['excerpt', 'body', 'author_name', 'seo_title', 'seo_description'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = strip_tags(trim($data[$field]));
            }
        }

        return $data;
    }
}
