<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\HasSlug;

class Genre extends Model
{
    protected $fillable = ['name', 'description'];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class);
    }

    use HasSlug;

    protected function slugSource(): string
    {
        return 'name';
    }
}
