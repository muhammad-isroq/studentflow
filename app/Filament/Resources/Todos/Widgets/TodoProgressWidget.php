<?php

namespace App\Filament\Resources\Todos\Widgets;

use App\Models\Todo;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class TodoProgressWidget extends Widget
{
    
    protected string $view = 'filament.resources.todos.widgets.todo-progress-widget';
    protected $listeners = ['update-todo-progress' => '$refresh'];

   
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::user();

        
        $totalTasks = Todo::where('user_id', $user->id)->count();

        
        $completedTasks = Todo::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();

        
        $percentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return [
            'total' => $totalTasks,
            'completed' => $completedTasks,
            'percentage' => $percentage,
        ];
    }
}