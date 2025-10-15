<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class BirthdayWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = '1';

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $today = now()->startOfDay();
                $sevenDaysFromNow = now()->startOfDay()->addDays(7);

                return User::query()
                    ->whereNotNull('tanggal_lahir')
                    // PERBAIKAN QUERY AGAR LEBIH ANDAL
                    ->where(function (Builder $query) use ($today, $sevenDaysFromNow) {
                        $startDayOfYear = $today->dayOfYear;
                        $endDayOfYear = $sevenDaysFromNow->dayOfYear;

                        if ($startDayOfYear <= $endDayOfYear) {
                            // Kasus normal (tidak melewati akhir tahun)
                            $query->whereRaw('DAYOFYEAR(tanggal_lahir) BETWEEN ? AND ?', [$startDayOfYear, $endDayOfYear]);
                        } else {
                            // Kasus melewati akhir tahun (misal: 28 Des - 4 Jan)
                            $query->whereRaw('DAYOFYEAR(tanggal_lahir) >= ?', [$startDayOfYear])
                                  ->orWhereRaw('DAYOFYEAR(tanggal_lahir) <= ?', [$endDayOfYear]);
                        }
                    })
                    ->orderByRaw('DAYOFYEAR(tanggal_lahir)');
            })
            ->heading('Upcoming Birthday (7 Days)')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Staff or Teacher Name'),

                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->label('Date of birth')
                    ->date('d F'),

                // Logika `getStateUsing` Anda sudah benar dan tidak perlu diubah
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Information')
                    ->getStateUsing(function (User $record): string {
                        if (!$record->tanggal_lahir) {
                            return '-';
                        }
                        
                        $birthDate = $record->tanggal_lahir;
                        $today = now()->startOfDay();
                        $birthdayThisYear = $birthDate->copy()->setYear($today->year);

                        if ($birthdayThisYear->lt($today)) {
                            $birthdayThisYear->addYear();
                        }

                        if ($birthdayThisYear->isSameDay($today)) {
                            return '🎉 Today!';
                        }
                        
                        $diff = $today->diffInDays($birthdayThisYear, false);

                        if ($diff === 1) {
                            return '🎂 Tomorrow';
                        }
                        
                        return "In {$diff} days";
                    })
                    ->badge()
                    ->color(function (User $record) {
                        if (!$record->tanggal_lahir) {
                            return 'gray';
                        }
                        
                        $birthDate = $record->tanggal_lahir;
                        $today = now()->startOfDay();
                        $birthdayThisYear = $birthDate->copy()->setYear($today->year);

                        if ($birthdayThisYear->isSameDay($today)) {
                            return 'success';
                        }

                        if ($today->diffInDays($birthdayThisYear, false) === 1) {
                            return 'info';
                        }
                        
                        return 'warning';
                    }),
            ])
            ->paginated(false);
    }
}

