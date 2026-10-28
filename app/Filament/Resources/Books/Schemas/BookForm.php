<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Enums\BookStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('author')
                    ->required(),
                Select::make('status')
                    ->options(BookStatus::class)
                    ->required(),
                TextInput::make('rating')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(5)
                    ->nullable(),
                Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('cover_image')
                    ->url()
                    ->nullable(),
                TextInput::make('isbn'),
            ]);
    }
}
