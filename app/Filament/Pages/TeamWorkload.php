<?php

namespace App\Filament\Pages;

use App\Models\Todo;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class TeamWorkload extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static string | \UnitEnum | null $navigationGroup = 'Collaboration';
    protected static ?string $title = 'Team Workload (This Week)';
    protected string $view = 'filament.pages.team-workload';

    public ?string $staffId = null;

  
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['staff', 'super_staff', 'admin']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('staffId')
                    ->label('Select Staff Member')
                    ->placeholder('Select a staff to view their workload...')
                    ->options(
                        User::whereHas('roles', function ($q) {
                            $q->whereIn('name', ['staff', 'super_staff', 'admin']);
                        })->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->live() 
                    ->afterStateUpdated(function () {
                        $this->resetTable(); 
                    }),
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(
                Todo::query()
                    
                    ->when(
                        $this->staffId,
                        fn (Builder $query) => $query
                            ->where('user_id', $this->staffId)
                           
                            ->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
                    )
                    
                    ->when(
                        !$this->staffId,
                        fn (Builder $query) => $query->whereNull('id') 
                    )
            )
            ->columns([
                TextColumn::make('task')
                    ->label('Task This Week')
                    ->weight('bold')
                    ->description(fn (Todo $record) => new HtmlString($record->description)),

                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'important' => 'warning',
                        'not_urgent' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('due_date')
                    ->date('l, d M') 
                    ->label('Due'),

                
                ToggleColumn::make('is_completed')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(), 
            ])
            ->paginated(false); 
    }

    
}