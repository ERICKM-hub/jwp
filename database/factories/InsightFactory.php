<?php

namespace Database\Factories;

use App\Models\Insight;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Insight>
 *
 * Test-only factory. Do not seed fictional articles into production.
 */
class InsightFactory extends Factory
{
    protected $model = Insight::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'excerpt' => null,
            'body' => null,
            'author_name' => null,
            'featured_image_path' => null,
            'published_at' => null,
            'is_published' => false,
            'is_featured' => false,
            'seo_title' => null,
            'seo_description' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'is_published' => true,
            'published_at' => now()->subDay(),
            'excerpt' => 'Confirmed excerpt for testing.',
            'body' => 'Confirmed body content for testing.',
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (): array => [
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'is_published' => true,
            'published_at' => now()->addDay(),
            'excerpt' => 'Scheduled excerpt for testing.',
            'body' => 'Scheduled body content for testing.',
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => [
            'is_featured' => true,
        ]);
    }
}
