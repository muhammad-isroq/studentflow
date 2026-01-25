<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\HtmlString;

class Dashboard extends BaseDashboard
{
    // Override function mount
    public function mount(): void
    {
        $user = auth()->user();

        // Cek: Apakah User adalah Guru? DAN Belum melihat notifikasi sesi ini?
        if ($user && $user->hasRole('guru') && !session()->has('pemberitahuan_guru_tampil')) {
            
            // Pemicu Action (Modal) agar langsung muncul
            $this->mountAction('peringatanAbsensi');

            // Tandai di session agar tidak muncul lagi saat refresh
            session()->put('pemberitahuan_guru_tampil', true);
        }
    }

    // Definisikan Action Modal-nya disini
    public function peringatanAbsensiAction(): Action
    {
        return Action::make('peringatanAbsensi')
            ->label('Attendance Warning')
            
            // Judul Modal (English)
            ->modalHeading('⚠️ Important Attendance Reminder')
            
            // Isi Pesan (English + HTML Formatting)
            ->modalDescription(new HtmlString('
                <div class="text-center">
                    <p class="mb-2 text-base">
                        Dear Teachers,
                    </p>
                    <p class="mb-4 text-base font-semibold text-red-600 dark:text-red-400">
                        If you are unable to attend class, please notify the Staff IMMEDIATELY regarding your substitute teacher.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        This allows the Staff to transfer meeting access, ensuring the substitute teacher can submit <b>Attendance</b> & <b>Lesson Plans</b> on time.
                    </p>
                </div>
            '))
            
            // Konfigurasi Tampilan Modal
            ->modalWidth('md') // Ukuran sedang
            ->modalAlignment(Alignment::Center) // Teks rata tengah
            ->modalSubmitAction(false) // Hilangkan tombol "Submit/Save"
            ->modalCancelActionLabel('I Understand') // Tombol Tutup (English)
            ->color('warning'); // Warna tombol/ikon
    }
}