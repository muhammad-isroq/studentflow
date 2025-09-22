<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Set; // <- import yang benar untuk Schema $set
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->label('Judul Artikel')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                FileUpload::make('image')
                    ->label('Gambar Utama')
                    ->image()
                    ->disk('public')
                    ->directory('article-images')
                    ->visibility('public'),
                Textarea::make('excerpt')
                    ->label('Ringkasan')
                    ->rows(3)
                    ->required(),

                RichEditor::make('body')
                    ->label('Isi Artikel')
                    ->required()
                    ->columnSpanFull(),

                DateTimePicker::make('published_at')
                    ->label('Waktu Publikasi')
                    ->nullable(),
            ]);
    }
}
