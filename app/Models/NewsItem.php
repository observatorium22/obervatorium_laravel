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
    'date_label',
    'published_on',
    'year',
    'body',
    'image_path',
    'position',
    'is_published',
])]
class NewsItem extends Model
{
    use HasMediaAssets;

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
            'is_published' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
