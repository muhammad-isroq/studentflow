<?php

namespace App\Filament\Resources\Todos\Pages;

use App\Filament\Resources\Todos\TodoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Todos\Widgets\TopUrgentTodos;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Todos\Widgets\TodoProgressWidget;

class ListTodos extends ListRecords
{
    protected static string $resource = TodoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add Task')
            ->icon('heroicon-o-plus'),
            
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // TopUrgentTodos::class,
            TodoProgressWidget::class,
        ];
    }

    // public function getTabs(): array
    // {
    //     return [

    //         'today' => Tab::make('Todo Today')
    //             ->icon('heroicon-m-sun')
    //             ->badge(
    //                 $this->getResource()::getEloquentQuery()
    //                     ->where('user_id', auth()->id()) 
    //                     ->whereDate('due_date', now()->today())
    //                     ->where('is_completed', false)
    //                     ->count()
    //             )
    //             ->modifyQueryUsing(fn (Builder $query) => $query
    //                 ->where('user_id', auth()->id()) 
    //                 ->whereDate('due_date', now()->today())
    //             ),


    //         'this_week' => Tab::make('To do this week')
    //             ->icon('heroicon-m-calendar')
    //             ->badge(
    //                 $this->getResource()::getEloquentQuery()
    //                     ->where('user_id', auth()->id()) 
    //                     ->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
    //                     ->where('is_completed', false)
    //                     ->count()
    //             )
    //             ->modifyQueryUsing(fn (Builder $query) => $query
    //                 ->where('user_id', auth()->id()) 
    //                 ->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
    //             ),

            
    //         'our_staff' => Tab::make('Todo our staff')
    //             ->icon('heroicon-m-users')
    //             ->modifyQueryUsing(fn (Builder $query) => $query
    //                 ->where('is_public', true) 
    //                 ->where('user_id', '!=', auth()->id()) 
    //             ),
    //     ];
    // }
}