<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\MediaLink;

class GameMedia extends Model
{
    protected $table = 'game_media';

    protected $fillable = ['game_id', 'type', 'source', 'path', 'link_url', 'position'];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    protected function url(): Attribute
    {
        return Attribute::get(
            fn() => $this->source === 'link' ? $this->link_url : asset('storage/' . $this->path)
        );
    }

    public function isLink(): bool
    {
        return $this->source === 'link';
    }

    public function isYoutube(): bool
    {
        return $this->type === 'video' && $this->source === 'link';
    }

    public function youtubeId(): ?string
    {
        return $this->isYoutube() ? MediaLink::youtubeId($this->link_url) : null;
    }

    public function youtubeThumb(): ?string
    {
        $id = $this->youtubeId();

        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
