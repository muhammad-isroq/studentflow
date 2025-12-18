<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Transactions\Widgets\TransactionOverview;
use Filament\Actions\Action;
use Filament\Actions;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('buku_kas')
            ->label('Cash Book')
            ->icon('heroicon-o-book-open') 
            ->color('success')
            ->url(TransactionResource::getUrl('cash-book')),
            Action::make('print_report')
            ->label('Print Report')
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->form([
                \Filament\Forms\Components\Select::make('month')
                    ->label('Bulan')
                    ->options([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                    ])
                    ->default(date('m'))
                    ->required(),
                
                \Filament\Forms\Components\TextInput::make('year')
                    ->label('Tahun')
                    ->numeric()
                    ->default(date('Y'))
                    ->required(),
            ])
            ->action(function (array $data) {
                // Redirect ke Route Print Controller dengan membawa parameter
                return redirect()->route('print.finance', [
                    'month' => $data['month'],
                    'year' => $data['year'],
                ]);
            }),
            CreateAction::make(),
Actions\Action::make('cek_tunggakan')
            ->label('Check Arrears') // Label tombol
            ->icon('heroicon-o-exclamation-triangle') // Icon peringatan
            ->color('danger') // Warna kuning/orange
            ->url(TransactionResource::getUrl('unpaid')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionOverview::class,
        ];
    }
}
