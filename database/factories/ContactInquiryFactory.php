<?php

namespace Database\Factories;

use App\Enums\ContactInquiryStatus;
use App\Models\ContactInquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactInquiry>
 */
class ContactInquiryFactory extends Factory
{
    protected $model = ContactInquiry::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->numerify('+2547########'),
            'organization' => fake()->optional()->company(),
            'subject' => fake()->optional()->sentence(4),
            'message' => fake()->paragraph(),
            'status' => ContactInquiryStatus::New,
            'submitted_at' => now(),
        ];
    }

    public function reviewed(): static
    {
        return $this->state(fn (): array => [
            'status' => ContactInquiryStatus::Reviewed,
        ]);
    }
}
