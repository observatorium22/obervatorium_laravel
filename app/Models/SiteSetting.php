<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'mission_statement',
    'instagram_url',
    'facebook_url',
    'linkedin_url',
    'featured_project_ids',
])]
class SiteSetting extends Model
{
    public const DEFAULT_INSTAGRAM_URL = 'https://www.instagram.com/observatoriumrotterdam/';

    public const DEFAULT_FACEBOOK_URL = 'https://www.facebook.com/ObservatoriumRotterdam';

    public const DEFAULT_LINKEDIN_URL = 'https://www.linkedin.com/company/observatorium-public-art-for-public-life';

    protected function casts(): array
    {
        return [
            'featured_project_ids' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'instagram_url' => self::DEFAULT_INSTAGRAM_URL,
            'facebook_url' => self::DEFAULT_FACEBOOK_URL,
            'linkedin_url' => self::DEFAULT_LINKEDIN_URL,
        ]);
    }
}
