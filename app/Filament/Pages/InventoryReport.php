<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\StockLog;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use App\Models\Inventory;
use Filament\Tables\Filters\SelectFilter;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;

class InventoryReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.pages.inventory-report';
    protected static ?string $title = 'Laporan Stok Inventaris';
    protected static ?string $slug = 'inventory-report';
    protected static UnitEnum | string | null $navigationGroup = 'Inventories';
    protected static ?int $navigationSort = 3; 

    public function table(Table $table): Table
    {
        return $table
            ->query(StockLog::query())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('inventory.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('change_amount')
                    ->label('Perubahan')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => $state < 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "+{$state}" : $state),
                TextColumn::make('stock_after_change')
                    ->label('Stok Akhir')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reason')
                    ->label('Alasan')
                    ->searchable(),
                ImageColumn::make('proof')
                    ->label('Bukti')
                    ->width(100)
                    ->height(100)
                    ->circular(),
                TextColumn::make('user.name') 
                    ->label('Dicatat oleh')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('inventory_id')
                    ->label('Barang')
                    ->options(Inventory::pluck('nama_barang', 'id'))
                    ->searchable(),
                SelectFilter::make('user_id')
                    ->label('Dicatat oleh')
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Dari Tanggal'),
                        DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['admin', 'staff']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(route('filament.admin.print.stock_report')) // Arahkan ke rute baru
                ->openUrlInNewTab(),
        ];
    }
}