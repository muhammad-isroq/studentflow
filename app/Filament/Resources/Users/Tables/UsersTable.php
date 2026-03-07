<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                    TextColumn::make('tanggal_lahir')
                    ->date('d M Y') // Format tampilan tanggal
                    ->sortable(),
                // TextColumn::make('email_verified_at')
                //     ->dateTime()
                //     ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                // TextColumn::make('email_verified_at')
                //     ->dateTime()
                //     ->sortable(),
                ImageColumn::make('photo')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset('storage/' . $record->photo))
                    ->width(100)
                    ->height(100)
                    ->circular(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('impersonate')
    ->label('Monitor')
    ->icon('heroicon-o-finger-print')
    ->color('warning')
    ->requiresConfirmation()
    ->action(function ($record) {
        
        app(\Lab404\Impersonate\Services\ImpersonateManager::class)->take(auth()->user(), $record);

        
        session()->forget(['password_hash_web', 'password_hash_guru']); 

       
        return redirect()->to('/studentflow'); 
    })
        
        ->visible(fn ($record) => 
    $record->id !== auth()->id() && 
    $record->roles->contains('name', 'guru') 
),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
