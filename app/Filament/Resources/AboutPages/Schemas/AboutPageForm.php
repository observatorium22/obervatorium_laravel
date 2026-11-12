<?php

namespace App\Filament\Resources\AboutPages\Schemas;

use App\Filament\Support\ContentFields;
use Filament\Schemas\Schema;

class AboutPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ContentFields::section('About', ContentFields::main(withSubtitle: false, withIntro: false, withBody2: false)),
                ContentFields::section('Files', ContentFields::media()),
            ]);
    }
}
