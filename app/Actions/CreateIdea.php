<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateIdea
{
    // public function __construct(#[CurrentUser] protected User $user)
    // {

    // }

    /**
     * Create an idea with its steps for the given user.
     *
     * @param  array{title: string, description?: string|null, status?: string|null, links?: array<int, string>|null, steps?: array<int, string>|null}  $attributes
     */
    public function handle(User $user, array $attributes, ?UploadedFile $image = null): Idea
    {
        return DB::transaction(function () use ($user, $attributes, $image): Idea {
            $idea = $user->ideas()->create(collect($attributes)
                ->except(['steps', 'image'])
                ->reject(fn (mixed $value): bool => $value === null)
                // ->put('status', $attributes['status'] ?? IdeaStatus::PENDING->value)
                ->put('image_path', $image?->store('ideas', 'public'))
                ->all());

            $idea->steps()->createMany(
                collect($attributes['steps'] ?? [])
                    ->map(fn (string $description): string => trim($description))
                    ->filter()
                    ->map(fn (string $description): array => ['description' => $description])
                    ->all()
            );

            return $idea;
        });
    }
}
