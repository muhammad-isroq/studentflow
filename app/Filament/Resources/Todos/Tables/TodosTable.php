<?php

namespace App\Filament\Resources\Todos\Tables;

use App\Models\Todo;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class TodosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            
            ->query(
                Todo::query()
                    ->where('user_id', auth()->id())
                    ->orderBy('is_completed', 'asc') 
                    ->orderBy('due_date', 'asc')     
            )

            
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])

            
            ->recordClasses(function (Todo $record) {
                $baseStyle = 'p-5 rounded-xl shadow-sm border transition duration-300 flex flex-col justify-between h-full hover:shadow-md';

                $colorStyle = match ($record->category) {
                    'urgent'    => 'bg-red-50 border-red-200 dark:bg-red-950/30 dark:border-red-800',
                    'important' => 'bg-amber-50 border-amber-200 dark:bg-amber-950/30 dark:border-amber-800',
                    'general'   => 'bg-blue-50 border-blue-200 dark:bg-blue-950/30 dark:border-blue-800',
                    default     => 'bg-white border-gray-200 dark:bg-gray-900 dark:border-gray-700',
                };

                return $baseStyle . ' ' . $colorStyle;
            })

            ->columns([
                Stack::make([
                    Split::make([
                        TextColumn::make('task')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Medium)
                            ->searchable(),

                        TextColumn::make('category')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'urgent' => 'danger',
                                'important' => 'warning',
                                'general' => 'gray', // General jadi abu-abu agar lebih soft
                                default => 'info',
                            })
                            ->grow(false),
                    ])->extraAttributes(['class' => 'items-start']),

                    
                    TextColumn::make('description')
                        ->color('gray')
                        
                        ->formatStateUsing(fn (string $state) => Str::limit(strip_tags($state), 100))
                        ->size(TextSize::Small)
                        ->extraAttributes(['class' => 'my-2']),

                    Split::make([
                        TextColumn::make('due_date')
                            ->icon('heroicon-m-calendar')
                            ->date('d M Y')
                            ->color(fn ($state) => $state < now() ? 'danger' : 'gray')
                            ->size(TextSize::Small),

                        CheckboxColumn::make('is_completed')
                            ->label('Done?')
                            ->afterStateUpdated(function ($livewire) {
                                
                                $livewire->dispatch('update-todo-progress');
                            })
                            ->extraAttributes(['class' => 'flex justify-end']),
                    ])->extraAttributes(['class' => 'mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 items-center']),
                ])->space(1),
            ])

            ->filters([
                
                Filter::make('focus_mode')
                    ->label('Fokus: Urgent & Baru Minggu Ini')
                    ->default() 
                    ->query(function (Builder $query) {
                        return $query->where(function (Builder $subQuery) {
                            $subQuery
                                ->where('category', 'urgent') 
                                ->orWhereBetween('created_at', [ 
                                    now()->startOfWeek(),
                                    now()->endOfWeek()
                                ]);
                        });
                    }),

                TernaryFilter::make('is_completed')
                    ->label('Status')
                    ->placeholder('All Tasks')
                    ->trueLabel('Completed')
                    ->falseLabel('Pending'),

                Filter::make('due_date')
                    ->form([
                        DatePicker::make('date_from')->label('Dari Tanggal'),
                        DatePicker::make('date_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($q, $date) => $q->whereDate('due_date', '>=', $date))
                            ->when($data['date_until'], fn ($q, $date) => $q->whereDate('due_date', '<=', $date));
                    })
                    
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['date_from'] || $data['date_until']) {
                            return 'Menampilkan Rekap Tanggal';
                        }
                        return null;
                    })
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-horizontal')
                ->color('gray')
                ->tooltip('Actions'),
            ])
            ->bulkActions([
                //  BulkActionGroup::make([
                //      DeleteBulkAction::make(),
                //  ]),
            ]);
    }
}