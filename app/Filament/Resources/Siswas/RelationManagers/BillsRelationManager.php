<?php

namespace App\Filament\Resources\Siswas\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class BillsRelationManager extends RelationManager
{
    protected static string $relationship = 'bills';
    protected static ?string $title = 'Riwayat Tagihan';
    protected string $view = 'filament.relation-managers.bills-grouped';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Jenis Tagihan'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tanggal Lunas')
                    ->dateTime('d M Y, H:i'),
            ])
            ->defaultSort('due_date', 'desc');
    }

    // Method untuk mendapatkan bills berdasarkan payment type
    public function getBillsByPaymentType()
    {
        $bills = $this->getOwnerRecord()->bills()
            ->with('paymentType')
            ->get()
            ->groupBy('paymentType.name');
            
        return $bills;
    }

    // Method khusus untuk mendapatkan SPP bulanan
    public function getMonthlySppBills()
    {
        $sppPaymentType = $this->getOwnerRecord()->bills()
            ->whereHas('paymentType', function ($query) {
                $query->where('name', 'like', '%spp%')
                      ->orWhere('name', 'like', '%SPP%')
                      ->orWhere('name', 'like', '%Monthly%');
            })
            ->with('paymentType')
            ->get();

        // Generate 12 bulan untuk tahun ini
        $months = [];
        $currentYear = date('Y');
        
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $monthDate = $currentYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            
            // Cari bill untuk bulan ini
            $monthBill = $sppPaymentType->first(function ($bill) use ($monthDate) {
                return $bill->due_date && $bill->due_date->format('Y-m') === $monthDate;
            });
            
            $months[] = [
                'month' => $monthName,
                'month_number' => $i,
                'year' => $currentYear,
                'bill' => $monthBill,
                'status' => $monthBill ? $monthBill->status : 'not_generated',
                'amount' => $monthBill ? $monthBill->amount : ($this->getOwnerRecord()->spp_amount ?? 0),
                'due_date' => $monthBill ? $monthBill->due_date : null,
                'paid_at' => $monthBill ? $monthBill->paid_at : null,
            ];
        }
        
        return collect($months);
    }

    // Method untuk mendapatkan payment types lainnya
    public function getNonSppBills()
    {
        return $this->getOwnerRecord()->bills()
            ->whereHas('paymentType', function ($query) {
                $query->where('name', 'not like', '%spp%')
                      ->where('name', 'not like', '%SPP%')
                      ->where('name', 'not like', '%Monthly%');
            })
            ->with('paymentType')
            ->get()
            ->groupBy('paymentType.name');
    }

    // Actions untuk handle button clicks
    public function markAsPaid($billId)
    {
        $bill = \App\Models\Bill::find($billId);
        if ($bill) {
            $bill->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            
            $this->dispatch('refresh');
            
            \Filament\Notifications\Notification::make()
                ->title('Tagihan berhasil ditandai sebagai lunas')
                ->success()
                ->send();
        }
    }

    public function generateSppBill($month, $year)
    {
        $siswa = $this->getOwnerRecord();
        
        // Cari atau buat payment type untuk SPP
        $sppPaymentType = \App\Models\PaymentType::firstOrCreate([
            'name' => 'Monthly SPP'
        ]);

        // Hitung tanggal jatuh tempo (misalnya tanggal billing_day setiap bulan)
        $billingDay = $siswa->billing_day ?? 10;
        $dueDate = \Carbon\Carbon::create($year, $month, $billingDay);

        // Buat tagihan baru
        $siswa->bills()->create([
            'payment_type_id' => $sppPaymentType->id,
            'amount' => $siswa->spp_amount ?? 500000,
            'due_date' => $dueDate,
            'status' => 'unpaid',
        ]);

        $this->dispatch('refresh');
        
        \Filament\Notifications\Notification::make()
            ->title('Tagihan SPP berhasil dibuat')
            ->body("Tagihan untuk bulan " . date('F', mktime(0, 0, 0, $month, 1)) . " $year")
            ->success()
            ->send();
    }

    public function editBill($billId)
    {
        // Redirect ke halaman edit atau buka modal
        $this->dispatch('open-modal', [
            'id' => 'edit-bill-modal',
            'billId' => $billId
        ]);
    }

    public function deleteBill($billId)
    {
        $bill = \App\Models\Bill::find($billId);
        if ($bill) {
            $bill->delete();
            
            $this->dispatch('refresh');
            
            \Filament\Notifications\Notification::make()
                ->title('Tagihan berhasil dihapus')
                ->success()
                ->send();
        }
    }

    public function createBill($paymentTypeName)
    {
        $this->dispatch('open-modal', [
            'id' => 'create-bill-modal',
            'paymentTypeName' => $paymentTypeName,
            'siswaId' => $this->getOwnerRecord()->id
        ]);
    }

    public function createNewPaymentTypeBill()
    {
        $this->dispatch('open-modal', [
            'id' => 'create-new-payment-type-modal',
        'siswaId' => $this->getOwnerRecord()->id
        ]);
    }

    public function refreshBills(): void
    {
        // Cukup panggil $this->js('location.reload()') atau biarkan kosong
        // Livewire biasanya cukup pintar untuk me-refresh parent
    }
}