<?php

namespace App\Filament\Resources\Todos\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Todo; // <--- UBAH INI: Import Model Todo Langsung
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class TopUrgentTodos extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->heading('🔥 High Priority Tasks (All Staff)')
            ->headerActions([
                CreateAction::make()
                    ->label('Assign Urgent Task')
                    ->icon('heroicon-m-plus')
                    ->modalHeading('Assign Urgent Task to Staff')
                    ->visible(fn () => auth()->user()->hasRole(['super_staff', 'admin']))
                    ->form([
                        TextInput::make('task')
                            ->required()
                            ->label('Task Title')
                            ->placeholder('What needs to be done ASAP?'),

                        Select::make('user_id')
                            ->label('Assign to Staff')
                            ->relationship(
                                name: 'user', 
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', function ($q) {
                                    $q->whereIn('name', ['staff', 'super_staff','admin']);
                                })
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Hidden::make('category')->default('urgent'),
                        
                        DatePicker::make('due_date')
                            ->label('Deadline')
                            ->native(false) 
                            ->required(),
                        
                        // Pastikan task ini public agar bisa dilihat semua orang di Team Todo
                        Hidden::make('is_public')->default(true),
                    ])
                    ->successNotificationTitle('Urgent task assigned successfully'),
            ])
            // --- PERBAIKAN DI SINI ---
            ->query(
                Todo::query() // Gunakan Model langsung, BUKAN Resource
                    ->with('user') // Eager load user agar lebih cepat
                    ->where('category', 'urgent') // Filter Urgent
                    ->where('is_completed', false) // Filter Belum Selesai
                    ->orderBy('due_date', 'asc') // Urutkan deadline terdekat
                    ->limit(10)
            )
            // -------------------------
            ->columns([
                Tables\Columns\TextColumn::make('task')
                    ->weight('bold')
                    ->searchable()
                    // Tampilkan avatar atau nama pemilik tugas
                    ->description(fn ($record) => 'Owner: ' . $record->user->name)
                    ->wrap(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigned To')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('due_date')
                    ->date('d M')
                    ->color('danger')
                    ->alignRight(),

                CheckboxColumn::make('is_completed')
                    ->label('Done')
                    ->disabled(fn ($record) => 
                        $record->user_id !== auth()->id() && !auth()->user()->hasRole(['admin', 'super_staff'])
                    ),
            ])
            ->paginated(false);
    }
}