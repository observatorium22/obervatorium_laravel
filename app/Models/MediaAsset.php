<?php

namespace App\Models;

use App\Support\Files;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'legacy_id',
    'kind',
    'title',
    'text',
    'path',
    'display_path',
    'width',
    'height',
    'position',
])]
class MediaAsset extends Model
{
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): ?string
    {
        return Files::url($this->path);
    }

    public function displayUrl(): ?string
    {
        return Files::url($this->display_path ?: $this->path);
    }
}
