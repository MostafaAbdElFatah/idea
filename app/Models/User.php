<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\IdeaStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

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
