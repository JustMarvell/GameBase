<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;
    protected ?array $libraryIdsCache = null;

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
    
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function library(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)->withTimestamps();
    }

    /** IDs of the games in this user's library (queried once per request). */
    public function libraryIds(): array
    {
        return $this->libraryIdsCache ??= $this->library()->pluck('games.id')->all();
    }

    public function hasInLibrary(int $gameId): bool
    {
        return in_array($gameId, $this->libraryIds());
    }
}
