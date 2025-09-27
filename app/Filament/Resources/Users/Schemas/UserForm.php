<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // Selalu hash password baru
                    ->dehydrated(fn ($state) => filled($state)) // Hanya simpan ke DB jika diisi
                    ->required(fn (string $context): bool => $context === 'create'), // Hanya wajib saat membuat user baru,
                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->required()
                    ->multiple(false),
                Select::make('guru_id')
                    ->label('Hubungkan ke Data Guru (Opsional)')
                    ->relationship('guru', 'nama_guru')
                    ->searchable()
                    ->preload()
                    ->helperText('Isi ini jika user yang dibuat adalah seorang guru.'),
                FileUpload::make('photo')
                    ->label('Foto Profil')
                    ->maxSize(10240)
                    ->image(),

                TextInput::make('position')
                    ->label('Jabatan'),

                TextInput::make('instagram_url')
                    ->label('URL Instagram')
                    ->url(),

                TextInput::make('linkedin_url')
                    ->label('URL LinkedIn')
                    ->url(),
            ]);
    }
}
