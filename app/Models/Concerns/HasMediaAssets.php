<?php

namespace App\Models\Concerns;

use App\Models\MediaAsset;
use App\Support\Files;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMediaAssets
{
    public function media(): MorphMany
    {
        return $this->morphMany(MediaAsset::class, 'mediable')->orderBy('position');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(MediaAsset::class, 'mediable')->where('kind', 'image')->orderBy('position');
    }

    public function pdfs(): MorphMany
    {
        return $this->morphMany(MediaAsset::class, 'mediable')->where('kind', 'pdf')->orderBy('position');
    }

    public function galleryPath(): ?string
    {
        if (filled($this->image_path)) {
            return $this->image_path;
        }

        $first = $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->first();

        if ($first === null) {
            return null;
        }

        return $first->display_path ?: $first->path;
    }

    public function galleryUrl(): ?string
    {
        return Files::url($this->galleryPath());
    }

    public function getRouteKey(): mixed
    {
        return $this->legacy_id ?? $this->getKey();
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        if ($field !== $this->getRouteKeyName() && $field !== 'legacy_id') {
            return parent::resolveRouteBindingQuery($query, $value, $field);
        }

        return $query->where(function ($query) use ($value) {
            $query->where($this->qualifyColumn('legacy_id'), $value)
                ->orWhere(function ($query) use ($value) {
                    $query->whereNull($this->qualifyColumn('legacy_id'))
                        ->where($this->getQualifiedKeyName(), $value);
                });
        });
    }
}
