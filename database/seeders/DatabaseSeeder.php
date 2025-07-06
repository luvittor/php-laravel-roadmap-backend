<?php

namespace Database\Seeders;

use App\Models\Column;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        $users_random = rand(5, 10);
        $newUsers = User::factory($users_random)->create();

        // Create columns for each user without duplicating year/month pairs
        $years = range(now()->year - 1, now()->year + 1);
        $months = range(1, 12);

        $newUsers->each(function ($user) use ($years, $months) {
            $pairs = [];
            foreach ($years as $y) {
                foreach ($months as $m) {
                    $pairs[] = ['year' => $y, 'month' => $m];
                }
            }

            shuffle($pairs);
            $columnsCount = rand(0, count($pairs));

            for ($i = 0; $i < $columnsCount; $i++) {
                $pair = $pairs[$i];
                Column::factory()->for($user)->create($pair);
            }
        });

        // Create cards for each column
        Column::all()->each(function ($column) {
            $cards_random = rand(0, 10);
            $column->cards()->saveMany(\App\Models\Card::factory($cards_random)->make());
        });
    }
}
