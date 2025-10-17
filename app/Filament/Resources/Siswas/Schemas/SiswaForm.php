<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Schemas\Schema;


class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Name')
                    ->required(),
                TextInput::make('kelas_disekolah')
                    ->label('grade'),
                FileUpload::make('foto_formulir')
                    ->label('Form registration')
                    ->imagePreviewHeight('250')
                    ->downloadable()
                    ->openable(),
                FileUpload::make('foto')
                    ->label('Photo')
                    ->default(null),
                TextInput::make('no_wali')
                    ->label('Parents number')
                    ->required(),
                Textarea::make('alamat')
                    ->label('Address')
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('tgl_lahir')
                    ->label('Date of birth'),
                DatePicker::make('tgl_masuk')
                    ->label('Date of entry'),
                TextInput::make('billing_day')
                    ->label('Monthly Tuition Fee Billing Date')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(28)
                    ->helperText('Enter the date (1-28) to generate SPP every month.'),
                TextInput::make('spp_amount')
                    ->label('Monthly Tuition Fee Amount')
                    ->prefix('Rp')
                    ->required()
                    // ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('spp_amount', $state ? number_format((int) str_replace('.', '', $state), 0, ',', '.') : null)
                    )
                    ->dehydrateStateUsing(fn ($state) =>
                        $state ? (int) str_replace('.', '', $state) : null
                    )
                    ->formatStateUsing(fn ($state) =>
                        $state ? number_format($state, 0, ',', '.') : null
                    ),
                DatePicker::make('tgl_registrasi')
                    ->label('Registration date')
                    ->required(),
                Select::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'nama_program')
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak aktif',
                        'graduated' => 'Lulus'
                    ])
                    ->required(),
                Section::make('Registration fee')
                ->schema([
                    TextInput::make('registration_fee')
                    ->label('Amount')
                    ->prefix('Rp')
                    // ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('registration_fee', $state ? number_format((int) str_replace('.', '', $state), 0, ',', '.') : null)
                    )
                    ->dehydrateStateUsing(fn ($state) =>
                        $state ? (int) str_replace('.', '', $state) : null
                    )
                    ->formatStateUsing(fn ($state) =>
                        $state ? number_format($state, 0, ',', '.') : null
                    ),
                    FileUpload::make('registration_proof')
                        ->label('Proof of payment')
                        ->imagePreviewHeight('250')
                        ->downloadable(),
                ])->columns(2)
                ->columnSpanFull(), 
            ]);
    }
}
