<?php

namespace App\Filament\Resources\TeamMembers\Pages;

use App\Filament\Resources\TeamMembers\TeamMemberResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTeamMember extends CreateRecord
{
    protected static string $resource = TeamMemberResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null) && filled($data['name'] ?? null)) {
            $data['slug'] = Str::slug((string) $data['name']);
        }

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
