<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TeamMember>
 *
 * Test-only factory. Do not seed fictional people into production.
 */
class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'position' => 'Consultant',
            'qualification' => null,
            'bio' => null,
            'photo_path' => null,
            'email' => null,
            'phone' => null,
            'linkedin_url' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function unpublished(): static
    {
        return $this->inactive();
    }
}
