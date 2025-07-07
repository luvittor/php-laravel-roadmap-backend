<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Column>
 */
class ColumnFactory extends Factory
{
    /**
     * Counter used to generate unique month/year combinations.
     */
    protected static int $sequence = 0;

    public static function resetSequence(): void
    {
        self::$sequence = 0;
    }

    public function definition(): array
    {
        $year = now()->year + intdiv(self::$sequence, 12);
        $month = (self::$sequence % 12) + 1;

        self::$sequence++;

        return [
            'year' => $year,
            'month' => $month,
        ];
    }
}
