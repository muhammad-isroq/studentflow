<?php

namespace App\Filament\Resources\Registrations\Schemas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Diri Calon Siswa')
            ->schema([
                TextInput::make('nama')->required(),
                TextInput::make('username')->required(),
                TextInput::make('grade')->label('Kelas Disekolah')->required(),
                DatePicker::make('tgl_lahir')->required(),
                Textarea::make('alamat')->required(),
                TextInput::make('no_wa_wali')->label('WhatsApp Wali')->tel()->required(),
                TextInput::make('agama')
                    ->label('Agama'),
                Select::make('jenis_kelamin')
                    ->options(['Laki-laki' => 'Laki-laki',
                                'Perempuan' => 'Perempuan']),
                TextInput::make('asal_sekolah')
                    ->label('Asal Sekolah'),
                TextInput::make('nama_orang_tua')
                    ->label('Nama Orang Tua'),
                TextInput::make('pekerjaan_orang_tua')
                    ->label('Pekerjaan Orang Tua'),
                TextInput::make('sumber_info')
                    ->label('Sumber Informasi'),
                TextInput::make('alasan_kursus')
                    ->label('Alasan Kursus'),
            ])->columns(2),

        Section::make('Dokumen & Verifikasi')
            ->schema([
                FileUpload::make('photo')
                    ->image()
                    ->disk('public')
                    ->directory('photos')
                    ->visibility('public')
                    ->openable() 
                    ->downloadable() 
                    ->previewable(true),
                FileUpload::make('bukti_pembayaran')
                    ->image()
                    ->disk('public')
                    ->directory('payments')
                    ->label('Bukti Transfer Pendaftaran')
                    ->visibility('public')
                    ->openable() 
                    ->downloadable() 
                    ->previewable(true),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'waiting_verification' => 'Waiting Verification',
                        'paid' => 'Paid (Lunas)',
                        'selection' => 'Proses Seleksi', 
                        'rejected' => 'Rejected (Ditolak)',
                        'announced' => 'Pengumuman Akhir',
                    ])
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state === 'paid') {
                            $set('status', 'selection');
                        }
                    }),
                Textarea::make('catatan_admin')
                ->label('Alasan Penolakan / Catatan Tambahan')
                ->placeholder('Contoh: Bukti transfer tidak terbaca, silakan upload ulang.')
                ->rows(3)
                ->visible(fn ($get) => $get('status') === 'rejected')
                ->required(fn ($get) => $get('status') === 'rejected')
                ->columnSpanFull(),
            ])->columns(2),
            Select::make('program_id')
                ->label('Penempatan Program')
                ->options(\App\Models\Program::all()->pluck('nama_program', 'id'))
                ->searchable()
                // Penting: Hanya wajib diisi jika statusnya 'announced'
                ->required(fn ($get) => $get('status') === 'announced')
                ->visible(fn ($get) => $get('status') === 'announced')
                ->live(),
            Grid::make(2)
    ->schema([
        TextInput::make('spp_amount')
                    ->label('Monthly Tuition Fee Amount')
                    ->prefix('Rp')
                    ->required()
                    // ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('spp_amount', $state ? number_format((int) str_replace('.', '', $state), 0, ',', '.') : null)
                    )
                    ->dehydrateStateUsing(fn ($state) =>
                        $state ? (int) str_replace('.', '', $state) : null
                    )
                    ->formatStateUsing(fn ($state) =>
                        $state ? number_format($state, 0, ',', '.') : null
                    ),
            
        TextInput::make('registration_fee')
                    ->label('Amount')
                    ->prefix('Rp')
                    // ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('registration_fee', $state ? number_format((int) str_replace('.', '', $state), 0, ',', '.') : null)
                    )
                    ->dehydrateStateUsing(fn ($state) =>
                        $state ? (int) str_replace('.', '', $state) : null
                    )
                    ->formatStateUsing(fn ($state) =>
                        $state ? number_format($state, 0, ',', '.') : null
                    ),

        DatePicker::make('tgl_masuk')
            ->label('Tanggal Resmi Masuk')
            ->default(now()),

        TextInput::make('billing_day')
            ->label('Tanggal Jatuh Tempo Tiap Bulan')
            ->numeric()
            ->minValue(1)
            ->maxValue(28)
            ->default(10)
            ->helperText('Contoh: 10 (Tagihan muncul setiap tanggal 10)'),
    ])
    ->visible(fn ($get) => $get('status') === 'announced') // Hanya muncul saat pengumuman
    ->columnSpanFull(),
            ]);
    }
}
