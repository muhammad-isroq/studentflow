<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

class CashBook extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = TransactionResource::class;

    protected string $view = 'filament.resources.transactions.pages.cash-book';

    protected static ?string $title = 'Buku Kas (Cash Book)';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    // Properti untuk Filter - gunakan Livewire properties
    public $selectedMonth;
    public $selectedYear;

    public function mount(): void
    {
        $this->selectedMonth = 'all'; 
        $this->selectedYear = date('Y');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('Filter Periode')
                ->icon('heroicon-o-funnel')
                ->color('gray')
                ->form([
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            'all' => 'Semua Bulan (1 Tahun)',
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                        ])
                        ->default($this->selectedMonth)
                        ->required(),

                    Select::make('year')
                        ->label('Tahun')
                        ->options([
                            '2023' => '2023',
                            '2024' => '2024',
                            '2025' => '2025',
                            '2026' => '2026',
                            '2027' => '2027',
                        ])
                        ->default($this->selectedYear)
                        ->required(),
                ])
                ->modalHeading('Filter Periode')
                ->modalSubmitActionLabel('Terapkan Filter')
                ->action(function (array $data) {
                    $this->selectedMonth = $data['month'];
                    $this->selectedYear = $data['year'];
                    $this->resetTable();
                }),
                
            Action::make('print_cash_book')
                ->label('Print Cash Book')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->modalWidth('md')
                ->form([
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            'all' => 'Semua Bulan (1 Tahun)', 
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                        ])
                        ->default($this->selectedMonth ?? date('m')) 
                        ->required(),

                    TextInput::make('year')
                        ->label('Tahun')
                        ->numeric()
                        ->default($this->selectedYear ?? date('Y')) 
                        ->required(),
                ])
                ->modalHeading('Pilih Periode Buku Kas')
                ->modalSubmitActionLabel('Cetak PDF')
                ->action(function (array $data) {
                    return redirect()->route('print.cash_book', [
                        'month' => $data['month'],
                        'year' => $data['year'],
                    ]);
                }),
        ];
    }

    // QUERY DATA UTAMA
    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Transaction::query();

                // 1. Selalu filter berdasarkan tahun
                $query->whereYear('date', $this->selectedYear ?? date('Y'));

                // 2. Filter bulan HANYA JIKA bukan 'all'
                if ($this->selectedMonth !== 'all') {
                    $query->whereMonth('date', $this->selectedMonth);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('date')
                    ->date('d M Y')
                    ->label('Tanggal')
                    ->sortable(),
                    
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
                    
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30),
                
                // KOLOM DEBIT (PEMASUKAN)
                TextColumn::make('debit')
                    ->label('Masuk (Debit)')
                    ->state(function ($record) {
                        return $record->type === 'income' ? $record->amount : 0;
                    })
                    ->money('IDR')
                    ->color('success'),

                // KOLOM KREDIT (PENGELUARAN)
                TextColumn::make('credit')
                    ->label('Keluar (Kredit)')
                    ->state(function ($record) {
                        return $record->type === 'expense' ? $record->amount : 0;
                    })
                    ->money('IDR')
                    ->color('danger'),
            ])
            ->defaultSort('date', 'asc');
    }

    // FUNGSI MENGHITUNG TOTAL (Untuk Tampilan Card)
    public function getStatsProperty()
    {
        $bulan = $this->selectedMonth;
        $tahun = $this->selectedYear ?? date('Y');

        // Query Dasar
        $queryIncome = Transaction::where('type', 'income')->whereYear('date', $tahun);
        $queryExpense = Transaction::where('type', 'expense')->whereYear('date', $tahun);

        // Jika bulan spesifik dipilih, tambahkan filter bulan
        if ($bulan !== 'all') {
            $queryIncome->whereMonth('date', $bulan);
            $queryExpense->whereMonth('date', $bulan);
        }

        $income = $queryIncome->sum('amount');
        $expense = $queryExpense->sum('amount');

        // Hitung Saldo Total (Semua Waktu) - "Total Saat Ini"
        $allIncome = Transaction::where('type', 'income')->sum('amount');
        $allExpense = Transaction::where('type', 'expense')->sum('amount');
        $currentBalance = $allIncome - $allExpense;

        return [
            'income' => $income,
            'expense' => $expense,
            'balance_period' => $income - $expense,
            'current_balance' => $currentBalance // Saldo Kas Real Saat Ini
        ];
    }
}