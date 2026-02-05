<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User; 
use App\Models\Article; 
use Livewire\WithPagination;

class Home extends Component
{
    public $teachers, $article;

    use WithPagination;

    public function mount()
    {
        $this->teachers = User::role('guru')->orderBy('name', 'asc')->get();
    }

    public function render()
    {
        $articles = Article::whereNotNull('published_at')
                            ->latest('published_at')
                            ->paginate(4);
        return view('livewire.home', [
            'articles' => $articles
        ]);
    }
}
