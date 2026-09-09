<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Idea>
 */
class IdeaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            //'user_id' => User::inRandomOrder()->value('id'),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(IdeaStatus::cases()),
            'links' => [fake()->url()],
        ];
    }

    public function withStatus(IdeaStatus $status): static
    {
        return $this->state(fn (array $attributes): array => ['status' => $status]);
    }

    public function pending(): static
    {
        return $this->withStatus(IdeaStatus::PENDING);
    }

    public function completed(): static
    {
        return $this->withStatus(IdeaStatus::COMPLETED);
    }

    public function archived(): static
    {
        return $this->withStatus(IdeaStatus::ARCHIVED);
    }

    public function withoutLinks(): static
    {
        return $this->state(fn (array $attributes): array => ['links' => []]);
    }
}
