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
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Concerns\HasSlug;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, HasSlug;
    protected ?array $libraryIdsCache = null;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'library_public',
        'website',
    ];

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
            'library_public' => 'boolean',
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

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn() => $this->avatar ? asset('storage/' . $this->avatar) : null);
    }

    public function developedGames(): HasMany
    {
        return $this->hasMany(Game::class, 'developer_id');
    }

    protected function slugSource(): string
    {
        return 'name';
    }
}
