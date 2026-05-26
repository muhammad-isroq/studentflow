<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')->label('Log Name')->sortable(),
                TextColumn::make('description')->label('Action')->wrap(),
                TextColumn::make('causer.name')->label('Done By')->sortable()->searchable(),
                TextColumn::make('subject_type')
                    ->label('Modul / Fitur')
                    ->formatStateUsing(function ($state) {
                        $basename = class_basename($state);
                        
                        $translations = [
                            'Bill' => 'Tagihan',
                            'Siswa' => 'Data Siswa',
                            'User' => 'Pengguna',
                            'PaymentType' => 'Tipe Pembayaran',
                            'Transaction' => 'Kas / Transaksi', // <-- Ditambahkan
                        ];

                        return $translations[$basename] ?? $basename;
                    }),
                TextColumn::make('subject_id')
                    ->label('Record')
                    ->formatStateUsing(function ($record) {
                        $modelName = class_basename($record->subject_type);

                        // 1. TANGANI LOG UNTUK AKSI 'DELETED'
                        if ($record->description === 'deleted') {
                            $oldData = $record->properties['old'] ?? [];
                            
                            if ($modelName === 'Bill') {
                                $amount = isset($oldData['amount']) ? " Rp" . number_format($oldData['amount'], 0, ',', '.') : '';
                                $bulanInfo = '';
                                if (isset($oldData['due_date'])) {
                                    $bulanInfo = ' (' . \Carbon\Carbon::parse($oldData['due_date'])->translatedFormat('F Y') . ')';
                                }
                                return "Tagihan ID #{$record->subject_id}{$bulanInfo}{$amount} (Dihapus)";
                            }

                            // Logika khusus format string dihapus untuk Transaction
                            if ($modelName === 'Transaction') {
                                $amount = isset($oldData['amount']) ? " Rp" . number_format($oldData['amount'], 0, ',', '.') : '';
                                $kategori = $oldData['category'] ?? 'Transaksi';
                                return "{$kategori}{$amount} (Dihapus)";
                            }
                            
                            if (isset($oldData['nama'])) return $oldData['nama'] . ' (Dihapus)';
                            if (isset($oldData['name'])) return $oldData['name'] . ' (Dihapus)';
                            if (isset($oldData['title'])) return $oldData['title'] . ' (Dihapus)';
                            
                            return "{$modelName} ID #{$record->subject_id} (Dihapus)";
                        }

                        // 2. CEK APAKAH MODEL MASIH ADA DI SISTEM
                        if (! class_exists($record->subject_type)) {
                            return $record->subject_id;
                        }

                        $model = $record->subject_type::find($record->subject_id);

                        // 3. JIKA MODEL SUDAH HILANG (TAPI INI LOG CREATED / UPDATED LAMA)
                        if (! $model) {
                            $attributes = $record->properties['attributes'] ?? [];
                            
                            if ($modelName === 'Bill') {
                                $amount = isset($attributes['amount']) ? " Rp" . number_format($attributes['amount'], 0, ',', '.') : '';
                                $bulanInfo = '';
                                if (isset($attributes['due_date'])) {
                                    $bulanInfo = ' (' . \Carbon\Carbon::parse($attributes['due_date'])->translatedFormat('F Y') . ')';
                                }
                                return "Tagihan #{$record->subject_id}{$bulanInfo}{$amount} (Telah Dihapus)";
                            }

                            // Logika khusus jika record Transaction lama tidak ditemukan
                            if ($modelName === 'Transaction') {
                                $amount = isset($attributes['amount']) ? " Rp" . number_format($attributes['amount'], 0, ',', '.') : '';
                                $kategori = $attributes['category'] ?? 'Transaksi';
                                return "{$kategori}{$amount} (Telah Dihapus)";
                            }
                            
                            if (isset($attributes['nama'])) return $attributes['nama'] . ' (Telah Dihapus)';
                            if (isset($attributes['name'])) return $attributes['name'] . ' (Telah Dihapus)';
                            
                            return "ID: {$record->subject_id} (Telah Dihapus)";
                        }

                        // 4. LOGIKA NORMAL (DATA NORMAL / MASIH ADA)
                        if ($model instanceof \App\Models\Bill) {
                            $kategori = $model->paymentType ? $model->paymentType->name : 'Tagihan Lainnya';
                            
                            $bulanTagihan = '';
                            if ($model->due_date) {
                                $bulanTagihan = ' (' . \Carbon\Carbon::parse($model->due_date)->translatedFormat('F Y') . ')';
                            }
                            
                            if ($model->siswa) {
                                if (stripos($kategori, 'spp') !== false) {
                                     return "{$kategori}{$bulanTagihan} - {$model->siswa->nama}";
                                }
                                return "{$kategori} - {$model->siswa->nama}";
                            }
                            
                            $penerima = $model->paid_by ? " (ke: {$model->paid_by})" : '';
                            return "{$kategori}{$penerima} #{$model->id}";
                        }

                        // Logika Normal untuk Transaction
                        if ($model instanceof \App\Models\Transaction) {
                            $amount = " Rp" . number_format($model->amount, 0, ',', '.');
                            return "{$model->category}{$amount}";
                        }

                        // Logika untuk model-model lainnya
                        if ($model->getAttribute('nama')) return $model->nama;
                        if ($model->getAttribute('name')) return $model->name;
                        if ($model->getAttribute('title')) return $model->title;

                        return "ID: {$record->subject_id}"; 
                    }),
                TextColumn::make('created_at')->label('Time')->dateTime()->sortable(),
                TextColumn::make('changes')
                    ->label('Changes / Perubahan')
                    ->formatStateUsing(function ($record) {
                        $changes = [];
                        $properties = $record->properties ?? [];

                        // 1. KAMUS TERJEMAHAN DIPERBARUI DENGAN KOLOM TRANSAKSI
                        $fieldLabels = [
                            'billing_day'      => 'Tanggal Tagihan',
                            'spp_amount'       => 'Nominal SPP',
                            'nama'             => 'Nama Siswa',
                            'amount'           => 'Nominal',
                            'due_date'         => 'Jatuh Tempo',
                            'status'           => 'Status',
                            'notes'            => 'Catatan',
                            'paid_by'          => 'Dibayar Oleh/Kepada',
                            'transaction_type' => 'Tipe Kas (Bill)',
                            'siswa_id'         => 'Data Siswa',
                            'payment_type_id'  => 'Kategori Pembayaran',
                            // Kolom tabel Transaction
                            'type'             => 'Jenis Kas',
                            'category'         => 'Kategori Transaksi',
                            'description'      => 'Deskripsi',
                            'date'             => 'Tanggal Transaksi',
                            'proof_image'      => 'Bukti Gambar',
                            'reference_type'   => 'Sumber Modul',
                        ];

                        if (isset($properties['attributes']) && isset($properties['old'])) {
                            foreach ($properties['attributes'] as $field => $newValue) {
                                if (in_array($field, ['updated_at', 'created_at', 'id', 'deleted_at', 'reference_id', 'user_id'])) {
                                    continue;
                                }

                                $oldValue = $properties['old'][$field] ?? null;

                                if ($oldValue != $newValue) {
                                    $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));

                                    $formatValue = function ($val) use ($field) {
                                        if (is_null($val) || $val === '') return '(Kosong)';
                                        if (is_bool($val)) return $val ? 'Ya' : 'Tidak';
                                        
                                        if (in_array($field, ['spp_amount', 'amount', 'registration_fee'])) {
                                            return 'Rp ' . number_format((float)$val, 0, ',', '.');
                                        }
                                        
                                        if (in_array($field, ['due_date', 'paid_at', 'date']) && strtotime($val)) {
                                            return \Carbon\Carbon::parse($val)->translatedFormat('d M Y');
                                        }

                                        // Menerjemahkan tipe kas
                                        if ($field === 'type' || $field === 'transaction_type') {
                                            return $val === 'income' ? 'Pemasukan' : ($val === 'expense' ? 'Pengeluaran' : $val);
                                        }

                                        return $val;
                                    };

                                    $formattedOld = $formatValue($oldValue);
                                    $formattedNew = $formatValue($newValue);

                                    $changes[] = "• {$label}: {$formattedOld} ➔ {$formattedNew}";
                                }
                            }
                        }

                        return implode("\n", array_unique($changes)) ?: '-';
                    })
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}