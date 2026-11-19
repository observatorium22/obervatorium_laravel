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

    public function gallerySrcSet(): ?string
    {
        $path = $this->galleryPath();
        $sharp = $this->sharpGalleryPath($path);

        if ($path === null || $sharp === null) {
            return null;
        }

        return Files::url($path).' 1x, '.Files::url($sharp).' 2x';
    }

    private function sharpGalleryPath(?string $path): ?string
    {
        if ($path === null || $path === '' || str_starts_with($path, 'uploads/')) {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');
        $name = basename($path);

        if (str_contains($name, '_bgr_') || ! preg_match('/^\d+_/', $name)) {
            return null;
        }

        $sharpName = preg_replace('/^(\d+)_/', '$1_bgr_', $name, 1);
        $directory = dirname($path);
        $candidate = ($directory === '.' ? '' : $directory.'/').$sharpName;

        return is_file(public_path('files/'.$candidate)) ? $candidate : null;
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
