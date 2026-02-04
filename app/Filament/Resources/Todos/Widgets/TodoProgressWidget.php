<?php

namespace App\Filament\Resources\Todos\Widgets;

use App\Models\Todo;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

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


        $totalToday = Todo::where('user_id', $user->id)
            ->whereDate('due_date', now()->today())
            ->count();

        $completedToday = Todo::where('user_id', $user->id)
            ->whereDate('due_date', now()->today())
            ->where('is_completed', true)
            ->count();
            
        $percentageToday = $totalToday > 0 ? round(($completedToday / $totalToday) * 100) : 0;

        return [
            'total' => $totalTasks,
            'completed' => $completedTasks,
            'percentage' => $percentage,
            
            'totalToday' => $totalToday,
            'completedToday' => $completedToday,
            'percentageToday' => $percentageToday,
        ];
    }
}