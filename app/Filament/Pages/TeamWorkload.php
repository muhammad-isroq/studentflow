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

// --- TAMBAHKAN IMPORT INI ---
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
// ----------------------------

class TeamWorkload extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    
    protected static ?string $navigationLabel = 'Todo by other staff'; 
    protected static ?string $title = 'Monitoring Staff Todo';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Work Management';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.team-workload';

    public ?string $staffId = null;

    public static function canAccess(): bool
    {

        return auth()->user()->hasRole(['admin', 'super_staff', 'staff']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('staffId')
                ->label('Pilih Staff')
                ->options(function () {
                    return User::whereHas('roles', function ($query) {
                        $query->whereIn('name', ['admin', 'staff', 'super_staff']);
                    })->pluck('name', 'id');
                })
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
                    ->where('is_public', true)
                    ->when(
                        $this->staffId,
                        fn (Builder $query) => $query->where('user_id', $this->staffId),
                        fn (Builder $query) => $query->whereNull('id') // Kosong jika belum pilih staff
                    )
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('task')
                    ->weight('bold')
                    ->searchable(), // Opsional: Tambah search agar lebih mudah
                
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'important' => 'warning',
                        'general' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('due_date')->date('d M Y'),
                
                IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            // --- TAMBAHKAN BAGIAN FILTER DI SINI ---
            ->filters([
                Filter::make('due_date_filter')
                    ->form([
                        DatePicker::make('date_from')->label('Dari Tanggal'),
                        DatePicker::make('date_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date) => $query->whereDate('due_date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date) => $query->whereDate('due_date', '<=', $date),
                            );
                    })
                    // Indikator filter aktif (Opsional, agar user sadar sedang memfilter)
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['date_from'])->toFormattedDateString();
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['date_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            // ---------------------------------------
            ->paginated(false);
    }
}