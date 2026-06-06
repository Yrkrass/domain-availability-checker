<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Enums\CheckMode;
use App\Enums\CheckMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Domain>
 */
class CheckSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'interval' => fake()->randomElement([5, 10, 15, 30, 60]),
            'timeout' => fake()->numberBetween(5, 30),
            'mode' => fake()->randomElement(CheckMode::cases()),
            'method' => fake()->randomElement(CheckMethod::cases()),
            'starts_at' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'is_running' => false,
            'is_active' => fake()->boolean(),
        ];
    }
}
