<?php

namespace App\Models;

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
        return Attribute::get(
            fn() => $this->cover ? asset('storage/' . $this->cover) : null
        );
    }

    public function media(): HasMany
    {
        return $this->hasMany(GameMedia::class)->orderBy('position');
    }
}
