<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    /** The column the slug is built from, e.g. 'title' or 'name'. */
    abstract protected function slugSource(): string;

    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            $source = $model->slugSource();

            // Create the slug, and refresh it when the title/name changes
            if (blank($model->slug) || $model->isDirty($source)) {
                $model->slug = $model->makeUniqueSlug((string) $model->{$source});
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $model = $this->where($field ?? 'slug', $value)->first();

        // Old links that still use the numeric ID
        if (! $model && ctype_digit((string) $value)) {
            $model = $this->whereKey($value)->first();
        }

        return $model;
    }

    protected function makeUniqueSlug(string $value): string
    {
        $name = Str::lower(class_basename($this));
        $base = Str::slug($value) ?: $name;

        if (ctype_digit($base)) {
            $base = "{$name}-{$base}";
        }

        $slug = $base;
        $i = 2;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}