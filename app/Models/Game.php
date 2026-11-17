<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    protected $fillable = [
        'genre_id',
        'title',
        'developer',
        'release_year',
        'platform',
        'rating',
        'cover',
        'description',
    ];

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }
}
