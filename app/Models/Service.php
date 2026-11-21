<?php

namespace App\Models;

use App\Support\FirmContent;
use App\Support\SiteSettings;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'slug',
    'short_description',
    'description',
    'icon',
    'featured',
    'is_active',
    'sort_order',
    'seo_title',
    'seo_description',
])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query
            ->where('featured', true)
            ->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function pageTitle(): string
    {
        if (filled($this->seo_title) && ! FirmContent::isPlaceholder($this->seo_title)) {
            return $this->seo_title;
        }

        return $this->title.' | '.SiteSettings::shortName();
    }

    public function pageDescription(): string
    {
        return FirmContent::serviceMetaDescription($this);
    }
}
