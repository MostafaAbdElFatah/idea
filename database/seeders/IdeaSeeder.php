<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Idea;
use Illuminate\Database\Seeder;

class IdeaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        Idea::factory()->count(200)->create(['user_id' => 2]);
        Idea::factory()->count(200)->create(['user_id' => 3]);

        //fetch all ids
        //$userIds = User::pluck('id')->toArray();

        //fetch all ids expect 1
        $userIds = User::where('id', '!=', 1)
            ->pluck('id')
            ->toArray();

        //fetch all ids expect 1, 2, 3
        // $userIds = User::whereNotIn('id', [1, 2, 3])
        //     ->pluck('id')
        //     ->toArray();

        ///random user_Id in each record in 600 record
        Idea::factory()
            ->count(600)
            ->state(fn() => [
                'user_id' => fake()->randomElement($userIds),
            ])
            ->create();

        ///same user_Id in all 600 record
        // Idea::factory()->count(600)->create([
        //     'user_id' => fake()->randomElement($userIds),
        // ]);
        // Idea::factory()->count(100)->create([
        //     'user_id' => User::inRandomOrder()->value('id'),
        // ]);
    }
}
