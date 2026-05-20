<?php

namespace App\Filament\Resources\Bills\Pages;

use App\Filament\Resources\Bills\BillResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditBill extends EditRecord
{
    protected static string $resource = BillResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
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
