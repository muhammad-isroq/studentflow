<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms; 
use Filament\Forms\Contracts\HasForms;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.change-password';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('current_password')
                    ->label('Password Saat Ini')
                    ->password()
                    ->required()
                    ->currentPassword(),
                TextInput::make('new_password')
                    ->label('Password Baru')
                    ->password()
                    ->required()
                    ->confirmed()
                    ->different('current_password'),
                TextInput::make('new_password_confirmation')
                    ->label('Konfirmasi Password Baru')
                    ->password()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($data['new_password']),
            'password_changed_at' => now(),
        ]);

        Notification::make()
            ->title('Password berhasil diubah')
            ->success()
            ->send();

        return redirect()->route('filament.admin.pages.dashboard');
    }
}
