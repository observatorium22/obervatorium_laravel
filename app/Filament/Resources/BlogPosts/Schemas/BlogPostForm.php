<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Filament\Support\ContentFields;
use Filament\Schemas\Schema;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ContentFields::section('Blog', ContentFields::main(withDate: true, withIntro: false, withBody2: false)),
                ContentFields::section('Files', ContentFields::media()),
            ]);
    }
}
