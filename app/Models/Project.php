<?php

namespace App\Models;

use App\Models\Concerns\HasMediaAssets;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'legacy_id',
    'title',
    'subtitle',
    'location',
    'year',
    'intro',
    'body',
    'body2',
    'image_path',
    'is_featured',
    'position',
    'is_published',
])]
class Project extends Model
{
    use HasMediaAssets;

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
