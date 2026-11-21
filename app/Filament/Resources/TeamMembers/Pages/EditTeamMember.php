<?php

namespace App\Filament\Resources\TeamMembers\Pages;

use App\Filament\Resources\TeamMembers\TeamMemberResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamMemberResource::class;

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
        foreach (['bio', 'qualification', 'position', 'email', 'phone', 'linkedin_url'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = strip_tags(trim($data[$field]));
            }
        }

        if (($data['linkedin_url'] ?? null) === '') {
            $data['linkedin_url'] = null;
        }

        return $data;
    }
}
