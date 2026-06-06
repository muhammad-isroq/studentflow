<?php

namespace App\Filament\Resources\Bills\Pages;

use App\Filament\Resources\Bills\BillResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;

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
                // Label, Ikon, dan Warna berubah dinamis tergantung ketersediaan bukti
                ->label(fn () => $this->record->proof_of_payment ? 'Lihat Bukti Struk' : 'Print Struk')
                ->icon(fn () => $this->record->proof_of_payment ? 'heroicon-o-photo' : 'heroicon-o-printer')
                ->color(fn () => $this->record->proof_of_payment ? 'success' : 'info')
                ->url(function () {
                    // Jika sudah ada struk tersimpan, langsung buka gambarnya
                    if ($this->record->proof_of_payment) {
                        return Storage::url($this->record->proof_of_payment);
                    }
                    
                    // Jika belum, arahkan ke pembuatan struk baru
                    return route('print.receipt', ['bill' => $this->record->id]);
                })
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->status === 'paid'),
                
            DeleteAction::make(),
        ];
    }
}