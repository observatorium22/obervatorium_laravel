<?php

namespace App\Filament\Resources\NewsItems\Schemas;

use App\Filament\Support\ContentFields;
use Filament\Schemas\Schema;

class NewsItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ContentFields::section('News', ContentFields::main(withDate: true, withIntro: false, withBody2: false)),
                ContentFields::section('Files', ContentFields::media()),
            ]);
    }
}
