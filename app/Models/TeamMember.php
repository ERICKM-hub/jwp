<?php

namespace App\Models;

use Database\Factories\TeamMemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'slug',
    'position',
    'qualification',
    'bio',
    'photo_path',
    'email',
    'phone',
    'linkedin_url',
    'sort_order',
    'is_active',
])]
class TeamMember extends Model
{
    /** @use HasFactory<TeamMemberFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function photoUrl(): ?string
    {
        if (! filled($this->photo_path)) {
            return null;
        }

        return asset('storage/'.$this->photo_path);
    }

    public function photoAlt(): string
    {
        return 'Portrait of '.$this->name;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'JW';
    }

    public function publishedEmail(): ?string
    {
        $email = is_string($this->email) ? trim($this->email) : null;

        if ($email === null || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $email;
    }

    public function publishedPhone(): ?string
    {
        $phone = is_string($this->phone) ? trim($this->phone) : null;

        return $phone !== null && $phone !== '' ? $phone : null;
    }

    public function publishedLinkedInUrl(): ?string
    {
        $url = is_string($this->linkedin_url) ? trim($this->linkedin_url) : null;

        if ($url === null || $url === '') {
            return null;
        }

        if (! Str::startsWith($url, 'https://')) {
            return null;
        }

        return $url;
    }
}
