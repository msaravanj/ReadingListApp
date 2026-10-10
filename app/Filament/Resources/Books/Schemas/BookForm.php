<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Enums\BookStatus;
use Filament\Forms\Components\FileUpload;
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
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->image(),
                TextInput::make('isbn'),
            ]);
    }
}
