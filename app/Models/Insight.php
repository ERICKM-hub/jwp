<?php

namespace App\Models;

use App\Support\FirmContent;
use App\Support\SiteSettings;
use Database\Factories\InsightFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'body',
    'author_name',
    'featured_image_path',
    'published_at',
    'is_published',
    'is_featured',
    'seo_title',
    'seo_description',
])]
class Insight extends Model
{
    /** @use HasFactory<InsightFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->published()->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isPubliclyVisible(): bool
    {
        return $this->is_published
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function featuredImageUrl(): ?string
    {
        if (! filled($this->featured_image_path)) {
            return null;
        }

        return asset('storage/'.$this->featured_image_path);
    }

    public function featuredImageAlt(): string
    {
        return $this->title;
    }

    public function publishedAuthorName(): ?string
    {
        $name = is_string($this->author_name) ? trim($this->author_name) : null;

        return $name !== null && $name !== '' ? $name : null;
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
        return FirmContent::insightMetaDescription(
            $this->seo_description ?: $this->excerpt,
            $this->title,
        );
    }
}
