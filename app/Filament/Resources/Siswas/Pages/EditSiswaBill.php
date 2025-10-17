<?php

namespace App\Filament\Resources\Siswas\Pages;

use App\Filament\Resources\Siswas\SiswaResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Bill;
use App\Models\Siswa;
use App\Models\PaymentType;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\DateTimePicker;

class EditSiswaBill extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = SiswaResource::class;
    
    // PERBAIKAN: Hapus 'static' untuk Filament 4
    protected string $view = 'filament.resources.siswas.pages.edit-siswa-bill';

    public ?array $data = [];
    public Bill $bill;
    public Siswa $record;

    public function mount(Siswa $record, Bill $billRecord): void
    {
        $this->record = $record;
        $this->bill = $billRecord;
        
        if ($this->bill->siswa_id !== $this->record->id) {
            abort(403, 'Unauthorized');
        }

        $this->form->fill($this->bill->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('amount')
                    ->label('Bill Amount')
                    ->prefix('Rp')
                    ->required()
                    // ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('registration_fee', $state ? number_format((int) str_replace('.', '', $state), 0, ',', '.') : null)
                    )
                    ->dehydrateStateUsing(fn ($state) =>
                        $state ? (int) str_replace('.', '', $state) : null
                    )
                    ->formatStateUsing(fn ($state) =>
                        $state ? number_format($state, 0, ',', '.') : null
                    ),
                FileUpload::make('proof_of_payment')
                    ->label('Proof of payment')
                    ->imagePreviewHeight('250')
                    ->downloadable()
                    ->openable(),
                DatePicker::make('due_date')
                    ->label('Due Date')
                    ->required(),
                Select::make('status')
                ->options([
                        'unpaid' => 'Belum Lunas',
                        'paid' => 'Lunas',
                ]),
                DateTimePicker::make('paid_at'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action('save')
                ->color('primary'),
            
            Action::make('cancel')
                ->label('Batal')
                ->url(SiswaResource::getUrl('edit', ['record' => $this->record]))
                ->color('gray'),
        ];
    }

    public function save(): void
    {
        $validated = $this->form->getState();
        
        $this->bill->update($validated);

        \Filament\Notifications\Notification::make()
            ->title('Tagihan berhasil diperbarui')
            ->success()
            ->send();

        $this->redirect(SiswaResource::getUrl('edit', ['record' => $this->record->id]));
    }
    
    public function getTitle(): string
    {
        return 'Edit Tagihan - ' . $this->record->nama;
    }

    public function getBreadcrumbs(): array
    {
        return [
            SiswaResource::getUrl('index') => 'Siswa',
            SiswaResource::getUrl('edit', ['record' => $this->record]) => $this->record->nama,
            '#' => 'Edit Tagihan',
        ];
    }
}