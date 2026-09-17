<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\IdeaStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The user's full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim("{$this->first_name} {$this->last_name}"));
    }

    /**
     * The user's initials, used when there is no profile image.
     */
    protected function initials(): Attribute
    {
        return Attribute::get(fn (): string => Str::upper(
            Str::substr((string) $this->first_name, 0, 1).Str::substr((string) $this->last_name, 0, 1)
        ));
    }

    /**
     * The public URL of the user's profile image, if any.
     */
    protected function profileImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->profile_image_path
            ? Storage::disk('public')->url($this->profile_image_path)
            : null);
    }

    /**
     * The public URL of the user's profile banner, if any.
     */
    protected function bannerImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->banner_image_path
            ? Storage::disk('public')->url($this->banner_image_path)
            : null);
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function statusCounts(): Collection
    {
        $counts = $this->ideas()
            ->selectRaw('status, COUNT(*) as count, SUM(COUNT(*)) OVER () as total_count')
            ->groupBy('status')
            ->get();

        $totalCount = (int) ($counts->first()?->total_count ?? 0);

        return collect(IdeaStatus::cases())
            ->mapWithKeys(fn (IdeaStatus $status) => [
                $status->value => (int) ($counts->firstWhere('status', $status->value)?->count ?? 0),
            ])
            ->put('all', $totalCount);
    }
}
