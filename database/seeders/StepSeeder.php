<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\Step;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        //fetch all ids
        $ideaIds = Idea::pluck('id')->toArray();

        ///random idea_Id in each record in 3000 record
        Step::factory()
            ->count(3000)
            ->state(fn() => [
                'idea_id' => fake()->randomElement($ideaIds),
            ])
            ->create();

        ///same idea_Id in all 3000 record
        // Step::factory()->count(3000)->create([
        //     'idea_id' => fake()->randomElement($userIds),
        // ]);
        // Step::factory()->count(3000)->create([
        //     'idea_id' => Idea::inRandomOrder()->value('id'),
        // ]);
    }
}
