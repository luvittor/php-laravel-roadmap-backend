<?php

namespace Database\Seeders;

use App\Models\Column;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::factory($users_random)->create();
       
        // Create columns for each user
        User::all()->each(function ($user) {
            $columns_random = rand(0, 24);
            $user->columns()->saveMany(Column::factory($columns_random)->make());
        });

        // Create cards for each column
        Column::all()->each(function ($column) {
            $cards_random = rand(0, 10);
            $column->cards()->saveMany(\App\Models\Card::factory($cards_random)->make());
        });
    }
}
