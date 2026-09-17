<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteAccount
{
    /**
     * Delete the user, their ideas and steps, and every image they uploaded.
     */
    public function handle(User $user): void
    {
        $paths = $user->ideas()
            ->whereNotNull('image_path')
            ->pluck('image_path')
            ->push($user->profile_image_path, $user->banner_image_path)
            ->filter()
            ->values()
            ->all();

        DB::transaction(fn () => $user->delete());

        Storage::disk('public')->delete($paths);
    }
}
