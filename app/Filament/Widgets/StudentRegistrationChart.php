<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Siswa;
use Carbon\Carbon;


class StudentRegistrationChart extends ChartWidget
{
    protected ?string $heading = 'Student Registration Chart';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'staff']);
    }

    protected function getData(): array
    {
         $data = [];
        $labels = [];

        // Loop untuk 12 bulan terakhir, dari 11 bulan lalu hingga bulan ini
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M'); // e.g., 'Aug'
            $year = $date->format('y');  // e.g., '25'

            // Hitung jumlah siswa yang terdaftar di bulan dan tahun tersebut
            $count = Siswa::whereYear('tgl_registrasi', $date->year)
                          ->whereMonth('tgl_registrasi', $date->month)
                          ->count();
            
            $data[] = $count;
            $labels[] = "{$month} '{$year}";
        }

        return [
            'datasets' => [
                [
                    'label' => 'Number of students',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgb(54, 162, 235)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }    
}
