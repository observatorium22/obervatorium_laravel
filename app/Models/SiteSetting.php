<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'mission_statement',
    'featured_project_ids',
])]
class SiteSetting extends Model
{
    protected function casts(): array
    {
        return [
            'featured_project_ids' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
