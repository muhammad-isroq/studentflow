<?php

namespace App\Filament\Resources\Todos\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\Todos\TodoResource;
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
            ->heading('🔥 High Priority Tasks')
            // Tambahkan Tombol "Assign Task" di Pojok Kanan Atas Widget
            ->headerActions([
                CreateAction::make()
                    ->label('Assign Urgent Task')
                    ->icon('heroicon-m-plus')
                    ->modalHeading('Assign Urgent Task to Staff')
                    
                    // 1. Validasi: Hanya Super Staff yang bisa lihat tombol ini
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
                        
  
                        Hidden::make('is_public')->default(true),
                    ])

                    ->successNotificationTitle('Urgent task assigned successfully'),
            ])
            ->query(
                TodoResource::getEloquentQuery()
                    ->where('category', 'urgent')
                    ->where('is_completed', false)
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('task')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn ($record) => 'Assigned to: ' . $record->user->name), // Info tambahan

                Tables\Columns\TextColumn::make('due_date')
                    ->date('d M')
                    ->color('danger')
                    ->alignRight(),

                CheckboxColumn::make('is_completed')
                    ->label('Done'),
            ])
            ->paginated(false);
    }
}