<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\ContentFields;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ContentFields::section('Project', [
                    ...ContentFields::main(withLocation: true),
                    Toggle::make('is_featured')->label('On the homepage'),
                ]),
                ContentFields::section('Files', ContentFields::media()),
            ]);
    }
}
