<?php

namespace App\Filament\Resources\Bills\Pages;

use App\Filament\Resources\Bills\BillResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}

protected function mutateFormDataBeforeCreate(array $data): array
{
    // Ambil months_count dari input manual atau request
    $months = (int)($this->data['months_count'] ?? 1);

    if ($months > 1) {
        // Hentikan proses simpan normal agar tidak membuat 1 tagihan raksasa
        // Kita akan buat secara manual di afterCreate
        return $data;
    }

    return $data;
}

protected function afterCreate(): void
{
    $record = $this->getRecord();
    $months = (int)($this->data['months_count'] ?? 1);

    if ($months > 1) {
        $siswaIds = $this->data['siswa_ids'];
        
        // Hapus record dummy yang baru saja dibuat
        $record->delete();

        // Helper untuk ekstrak string dari array FileUpload
        $extractFile = function ($data) {
            if (is_array($data)) {
                return $data[0] ?? null; // Ambil elemen pertama jika berupa array
            }
            return $data;
        };

        $proofPath = $extractFile($this->data['proof_of_payment'] ?? null);
        $transferPath = $extractFile($this->data['transfer_proof'] ?? null);

        for ($i = 0; $i < $months; $i++) {
            $dueDate = \Carbon\Carbon::parse($this->data['due_date'])->addMonths($i);
            
            foreach ($siswaIds as $siswaId) {
                $sppAmount = \App\Models\Siswa::find($siswaId)->spp_amount;
                
                $bill = \App\Models\Bill::create([
                    'payment_type_id'  => $this->data['payment_type_id'],
                    'transaction_type' => $this->data['transaction_type'],
                    'amount'           => $sppAmount,
                    'due_date'         => $dueDate,
                    'status'           => $this->data['status'],
                    'paid_at'          => $this->data['paid_at'] ?? now(),
                    'notes'            => ($this->data['notes'] ?? 'Pembayaran SPP'),
                    'paid_by'          => $this->data['paid_by'],
                    // Simpan path string yang sudah diekstrak
                    'proof_of_payment' => $proofPath, 
                    'transfer_proof'   => $transferPath,
                ]);
                
                $bill->siswa()->attach($siswaId);
            }
        }
    }
}
}
