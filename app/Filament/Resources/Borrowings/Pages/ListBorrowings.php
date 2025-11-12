<?php

namespace App\Filament\Resources\Borrowings\Pages;

use App\Filament\Resources\Borrowings\BorrowingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListBorrowings extends ListRecords
{
    protected static string $resource = BorrowingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('print')
            ->label('Print Report')
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->url(route('filament.admin.print.borrowings')) 
            ->openUrlInNewTab(),
        ];
    }

    // public function getTabs(): array
    // {
        
    // }
}
