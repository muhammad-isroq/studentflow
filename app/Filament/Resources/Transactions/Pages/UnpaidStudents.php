<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\Page;
use App\Models\Bill;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use App\Filament\Resources\Siswas\SiswaResource;
use Filament\Notifications\Notification;


class UnpaidStudents extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = TransactionResource::class;

    protected string $view = 'filament.resources.transactions.pages.unpaid-students';

    protected static ?string $title = 'Data Tunggakan Siswa';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationCircle;

    public function getTotalTunggakanProperty()
    {
        // 1. Ambil state filter dari tabel (Livewire Property)
        $filters = $this->tableFilters;

        // 2. Mulai Query Dasar (Unpaid & Overdue)
        $query = Bill::whereIn('status', ['unpaid', 'overdue']);

        // 3. Cek Filter Bulan
        // (Jika user memilih bulan, kita filter. Jika tidak, ambil semua)
        if (isset($filters['due_month']['value']) && $filters['due_month']['value']) {
            $query->whereMonth('due_date', $filters['due_month']['value']);
        }

        // 4. Cek Filter Tahun
        // (Jika user memilih tahun, kita filter. Jika null, kita pakai default tahun ini sesuai settingan tabel)
        if (isset($filters['due_year']['value']) && $filters['due_year']['value']) {
            $query->whereYear('due_date', $filters['due_year']['value']);
        } else {
            // Fallback: Jika filter tahun belum disentuh, asumsikan tahun ini (sesuai default di tabel)
            $query->whereYear('due_date', date('Y'));
        }

        // 5. Kembalikan total nominalnya
        return $query->sum('amount');
    }

    public function table(Table $table): Table
    {
       return $table
            ->query(
                Bill::query()
                    ->whereIn('status', ['unpaid', 'overdue'])
            )
            ->columns([
                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->color('primary') 
                    ->weight('bold')
                    ->url(function ($record) {
                        if ($record->siswa_id) {
                            return SiswaResource::getUrl('edit', ['record' => $record->siswa_id]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab(),

                TextColumn::make('paymentType.name')
                    ->label('Jenis Tagihan')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color('danger'),
                
                TextColumn::make('status')
                    ->badge()
                    ->color('danger')
            ])
            ->filters([
                SelectFilter::make('due_month')
                    ->label('Filter Bulan Jatuh Tempo')
                    ->options([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                    ])
                    ->query(fn (Builder $query, array $data) => 
                        $query->when($data['value'], fn ($q) => $q->whereMonth('due_date', $data['value']))
                    ),

                SelectFilter::make('due_year')
                    ->label('Filter Tahun')
                    ->options([
                        '2024' => '2024',
                        '2025' => '2025',
                        '2026' => '2026',
                    ])
                    ->default(date('Y'))
                    ->query(fn (Builder $query, array $data) => 
                        $query->when($data['value'], fn ($q) => $q->whereYear('due_date', $data['value']))
                    ),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('due_date', 'asc');
    }
}
