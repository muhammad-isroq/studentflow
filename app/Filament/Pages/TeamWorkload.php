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
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class TeamWorkload extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    
    // UBAH LABEL INI SESUAI REQUEST ANDA:
    protected static ?string $navigationLabel = 'Todo Staff Lain'; 
    protected static ?string $title = 'Monitoring Staff Todo';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Work Management';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.team-workload';

    public ?string $staffId = null;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('staffId')
                ->label('Pilih Staff')
                ->options(User::pluck('name', 'id')) // Ambil semua user
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn () => $this->resetTable()),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Todo::query()
                    ->when(
                        $this->staffId,
                        fn (Builder $query) => $query->where('user_id', $this->staffId),
                        fn (Builder $query) => $query->whereNull('id') // Kosong jika belum pilih
                    )
            )
            ->columns([
                TextColumn::make('task')->weight('bold'),
                TextColumn::make('category')->badge(),
                TextColumn::make('due_date')->date('d M Y'),
                IconColumn::make('is_completed')->boolean()->label('Status'),
            ])
            ->paginated(false);
    }
}