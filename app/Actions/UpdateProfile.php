<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateProfile
{
    /**
     * Update the user's name and profile image.
     *
     * A new image replaces the old one, whose file is then deleted.
     *
     * @param  array{first_name: string, last_name: string}  $attributes
     */
    public function handle(User $user, array $attributes, ?UploadedFile $profileImage = null): User
    {
        $previousImagePath = $user->profile_image_path;

        $user->fill([
            'first_name' => $attributes['first_name'],
            'last_name' => $attributes['last_name'],
        ]);

        if ($profileImage) {
            $user->profile_image_path = $profileImage->store('profile-images', 'public');
        }

        $user->save();

        if ($profileImage && $previousImagePath !== null) {
            Storage::disk('public')->delete($previousImagePath);
        }

        return $user;
    }
}
