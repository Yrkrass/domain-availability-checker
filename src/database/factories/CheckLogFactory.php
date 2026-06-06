<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\CheckLog;
use App\Enums\ResponseCode;
use App\Models\CheckSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CheckLog>
 */
class CheckLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = fake()->randomElement([...ResponseCode::cases(), null]);

        return [
            'domain_id' => Domain::factory(),
            'check_setting_id' => CheckSetting::factory(),
            'is_available' => $code === ResponseCode::Ok,
            'response_code' => $code,
            'response_time' => fake()->numberBetween(100, 5000),
            'error' => in_array($code, [
                ResponseCode::MovedPermanently,
                ResponseCode::Found,
                ResponseCode::NotFound,
                ResponseCode::InternalServerError,
                null,
            ]) ? fake()->sentence() : null,
        ];
    }
}
