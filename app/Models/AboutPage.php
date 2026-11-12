<?php

namespace App\Models;

use App\Models\Concerns\HasMediaAssets;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'legacy_id',
    'title',
    'body',
    'image_path',
    'position',
    'is_published',
])]
class AboutPage extends Model
{
    use HasMediaAssets;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
