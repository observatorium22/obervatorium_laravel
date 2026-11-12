<?php

namespace App\Filament\Resources\NewsItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date_label')->label('Date')->sortable(),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('year')->sortable(),
                IconColumn::make('is_published')->boolean()->label('Published'),
            ])
            ->defaultSort('position')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
