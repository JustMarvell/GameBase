<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMedia extends Model
{
    protected $table = 'game_media';

    protected $fillable = ['game_id', 'type', 'path', 'position'];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn() => asset('storage/' . $this->path));
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
