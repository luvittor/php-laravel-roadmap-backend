<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Column>
 */
class ColumnFactory extends Factory
{
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'month' => fake()->numberBetween(1, 12),
            'user_id' => User::factory(),
        ];
    }
}
