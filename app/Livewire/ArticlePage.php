<?php

namespace App\Livewire;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;

class ArticlePage extends Component
{
    use WithPagination; 

    public function render()
    {
        $articles = Article::with('user') 
                            ->whereNotNull('published_at')
                            ->latest('published_at')
                            ->paginate(6);

        return view('livewire.article-page', [
            'articles' => $articles
        ]);
    }
}