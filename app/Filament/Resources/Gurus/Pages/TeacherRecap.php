<?php

namespace App\Filament\Resources\Gurus\Pages;

use App\Filament\Resources\Gurus\GuruResource;
use Filament\Resources\Pages\Page;
use App\Models\Guru;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class TeacherRecap extends Page
{
    protected static string $resource = GuruResource::class;
    
    protected static ?string $title = 'Rekap Mengajar';
    
    protected string $view = 'filament.resources.gurus.pages.teacher-recap';

    // Properti untuk menampung data yang akan ditampilkan
    public ?Guru $record = null;
    public Collection $programsWithCounts;

    // Method 'mount' akan mengambil data dari URL
    public function mount(int|string|Guru $record): void
    {
        // Jika $record sudah berupa object Guru, langsung gunakan
        if ($record instanceof Guru) {
            $this->record = $record;
        } else {
            // Jika masih ID, cari dulu
            $this->record = Guru::findOrFail($record);
        }

        // Ambil semua program yang dimiliki guru ini,
        // dan hitung jumlah 'classSessions' untuk setiap program secara efisien
        $this->programsWithCounts = $this->record->programs()
            ->withCount(['classSessions' => function (Builder $query) {
                // Hanya hitung sesi di mana guru utama adalah guru ini
                $query->where('guru_id', $this->record->id)
                      // DAN tidak ada guru pengganti
                      ->whereNull('replacement_guru_id');
            }])
            ->get();
    }

    // Atur judul halaman secara dinamis
    public function getTitle(): string | Htmlable
    {
        if ($this->record) {
            return 'Rekap Mengajar untuk ' . $this->record->nama_guru;
        }
        
        return 'Rekap Mengajar';
    }
    
    // Atur breadcrumbs untuk navigasi yang mudah
    public function getBreadcrumbs(): array
    {
        return [
            GuruResource::getUrl('index') => 'Guru',
            '#' => 'Rekap Mengajar',
        ];
    }
}