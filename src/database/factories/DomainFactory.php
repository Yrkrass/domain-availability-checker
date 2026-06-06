<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Domain>
 */
class DomainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'url' => 'https://'.$this->faker->domainName(),
            'is_available' => fake()->boolean(),
            'checked_at' => fake()->dateTimeBetween('-1 month'),
        ];
    }
}
