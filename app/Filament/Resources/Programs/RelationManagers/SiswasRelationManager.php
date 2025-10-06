<?php

namespace App\Filament\Resources\Programs\RelationManagers;

use App\Filament\Resources\Siswas\SiswaResource;
use App\Models\Siswa;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Actions\BulkAction;


class SiswasRelationManager extends RelationManager
{
    protected static string $relationship = 'siswas';

    protected static ?string $relatedResource = SiswaResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('name'),
                Tables\Columns\TextColumn::make('kelas_disekolah')->label('grade'),
            ])
            ->headerActions([
                Action::make('addSiswa')
                    ->label('Add Student')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('students')
                            ->label('Student')
                            ->multiple()
                            ->options(
                                // Opsi hanya siswa yang belum punya program
                                Siswa::whereNull('program_id')->pluck('nama', 'id')
                            )
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Ambil ID program saat ini
                        $programId = $this->getOwnerRecord()->id;
                        
                        // Update program_id untuk semua siswa yang dipilih
                        Siswa::whereIn('id', $data['students'])->update([
                            'program_id' => $programId
                        ]);
                    }),
            ])
            ->actions([
                // Ganti DetachAction dengan Action kustom
                Action::make('remove')
                    ->label('Remove')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Remove Student from Program')
                    ->modalDescription('Are you sure you want to remove this student from the program? This will not delete the student data.')
                    ->action(function (Siswa $record) {
                        // Set program_id menjadi null untuk melepaskan siswa dari program
                        $record->update(['program_id' => null]);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Ganti DetachBulkAction dengan BulkAction kustom
                    BulkAction::make('remove_selected')
                        ->label('Remove Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            // Loop semua record yang dipilih dan update program_id menjadi null
                            $records->each->update(['program_id' => null]);
                        }),
                ]),
            ]);
    }
}
