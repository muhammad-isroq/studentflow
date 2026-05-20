<?php

namespace App\Filament\Resources\Bills\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Actions\Action;


class BillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('payment_type_id')
                    ->relationship('paymentType', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->label('Bill Amount')
                    ->prefix('Rp')
                    ->required()
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
                FileUpload::make('proof_of_payment')
                    ->label('Proof of payment')
                    ->imagePreviewHeight('250')
                    ->disk('public')
                    ->directory('proofs')
                    ->downloadable()
                    ->openable(),
                DatePicker::make('due_date')
                    ->label('Due Date')
                    ->required(),
                Select::make('status')
                ->options([
                        'unpaid' => 'Belum Lunas',
                        'paid' => 'Lunas',
                         ])
                    ->required()
                    ->default('unpaid'),
                DateTimePicker::make('paid_at'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('Print Struk')
                ->icon('heroicon-o-printer')
                ->color('info')
                // Mengambil ID dari record yang sedang diedit
                ->url(fn () => route('print.receipt', ['bill' => $this->record->id]))
                ->openUrlInNewTab()
                // Tombol hanya muncul jika status tagihan sudah 'paid' (lunas)
                ->visible(fn () => $this->record->status === 'paid'),
            DeleteAction::make(),
        ];
    }
}
