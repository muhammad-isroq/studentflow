<?php

namespace App\Filament\Pages;

use App\Models\ClassSession;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class ViewLessonPlan extends Page
{
    
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'lesson-plan/{record}';
    protected string $view = 'filament.pages.view-lesson-plan';

    // Variabel ini akan dikirim ke Blade
    public ClassSession $record;

    public function mount($record): void
    {
        // Logika "Anti-Error" yang kita buat sebelumnya (Menangani ID vs Object)
        if ($record instanceof ClassSession) {
            $this->record = $record;
        } else {
            $this->record = ClassSession::findOrFail($record);
        }
    }

    public function getTitle(): string
    {
        return 'Lesson Plan: ' . $this->record->session_date->format('d F Y');
    }
}