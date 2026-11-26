<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Game extends Model
{
    protected $fillable = [
        'genre_id',
        'title',
        'developer',
        'release_year',
        'cover',
        'description',
        'cover_link',
        'cover_focus_x',
        'cover_focus_y',
        'developer_id',
    ];

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->cover) {
                return asset('storage/' . $this->cover);
            }

            return $this->cover_link ?: null;
        });
    }

    public function media(): HasMany
    {
        return $this->hasMany(GameMedia::class)->orderBy('position');
    }

    protected function coverPosition(): Attribute
    {
        return Attribute::get(
            fn() => ($this->cover_focus_x ?? 50) . '% ' . ($this->cover_focus_y ?? 50) . '%'
        );
    }

    /** Admins manage every game; a developer only manages their own. */
    public function isManagedBy(User $user): bool
    {
        return $user->hasRole('admin') || ($this->developer_id !== null && (int) $this->developer_id === $user->id);
    }

    use HasSlug;

    protected function slugSource(): string
    {
        return 'title';
    }

    public function developerAccount(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
