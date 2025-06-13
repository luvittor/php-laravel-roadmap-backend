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
            'year' => fake()->numberBetween(now()->year - 1, now()->year + 1),
            'month' => fake()->numberBetween(1, 12),
        ];
    }
}
