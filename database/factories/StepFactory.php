<?php

namespace Database\Factories;

use App\Models\Idea;
use App\Models\Step;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Step>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //'idea_id' => Idea::factory(),
            'idea_id' => Idea::inRandomOrder()->value('id'),
            'description' => fake()->sentence(),
            'completed' => fake()->boolean(),
        ];
    }
}
