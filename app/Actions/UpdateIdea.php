<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateIdea
{
    /**
     * Update an idea, sync its steps, and replace its image when a new one is given.
     *
     * Steps whose description is kept also keep their completed state.
     *
     * @param  array{title: string, description?: string|null, status?: string|null, links?: array<int, string>|null, steps?: array<int, string>|null}  $attributes
     */
    public function handle(Idea $idea, array $attributes, ?UploadedFile $image = null): Idea
    {
        $previousImagePath = $idea->image_path;

        DB::transaction(function () use ($idea, $attributes, $image): void {
            $idea->update(collect($attributes)
                ->only(['title', 'description', 'status', 'links'])
                ->put('description', $attributes['description'] ?? null)
                ->put('links', $attributes['links'] ?? [])
                ->reject(fn (mixed $value, string $key): bool => $key === 'status' && $value === null)
                ->when($image, fn (Collection $data): Collection => $data->put('image_path', $image->store('ideas', 'public')))
                ->all());

            $this->syncSteps($idea, $attributes['steps'] ?? []);
        });

        if ($image && $previousImagePath !== null) {
            Storage::disk('public')->delete($previousImagePath);
        }

        return $idea;
    }

    /**
     * Keep matching steps, remove the ones no longer listed, and create new ones.
     *
     * @param  array<int, string>  $descriptions
     */
    private function syncSteps(Idea $idea, array $descriptions): void
    {
        $descriptions = collect($descriptions)
            ->map(fn (string $description): string => trim($description))
            ->filter()
            ->unique()
            ->values();

        $idea->steps()->whereNotIn('description', $descriptions)->delete();

        $existing = $idea->steps()->pluck('description');

        $idea->steps()->createMany(
            $descriptions
                ->diff($existing)
                ->map(fn (string $description): array => ['description' => $description])
                ->all()
        );
    }
}
