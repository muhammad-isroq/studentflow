<?php

namespace App\Filament\Resources\TeacherRecapArchives\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class TeacherRecapArchiveInfolist
{

    public static array $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->description('Data rekapan arsip bulanan (Snapshot)')
                    ->schema([
                        TextEntry::make('guru.nama_guru')->label('Nama Guru')->size('text-xl')->weight('bold'),
                        TextEntry::make('month')
                            ->label('Periode')
                            ->formatStateUsing(fn ($record) => self::$months[$record->month] . ' ' . $record->year),
                        TextEntry::make('total_sessions')->label('Total Sesi Keseluruhan'),
                    ])->columns(3),
                    Section::make('Rincian Program (Membaca Data Masa Lalu)')
                    ->description('Data ini adalah rekaman permanen. Perubahan nama program di masa kini tidak akan merubah riwayat di bawah ini.')
                    ->schema([
                        RepeatableEntry::make('program_details')
                            ->label('')
                            ->schema([
                                TextEntry::make('nama_program')
                                    ->label('Nama Program')
                                    ->weight('bold')
                                    ->color('primary'),
                                    
                                TextEntry::make('total_sessions')
                                    ->label('Jumlah Pertemuan'),
                                    
                                TextEntry::make('total_teaching_minutes')
                                    ->label('Durasi Mengajar')
                                    ->formatStateUsing(function ($state) {
                                        $jam = floor($state / 60);
                                        $menit = $state % 60;
                                        return "{$jam} Jam {$menit} Menit";
                                    }),
                            ])
                            ->columns(3)
                    ]),
            ]);
    }
}
