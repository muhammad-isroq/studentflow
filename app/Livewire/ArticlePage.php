<?php

namespace App\Livewire;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;

class ArticlePage extends Component
{
    use WithPagination; //paginasi

    public function render()
    {
        // Ambil artikel yang sudah dipublikasi, urutkan dari yang terbaru, dan tampilkan 6 per halaman
        $articles = Article::whereNotNull('published_at')
                            ->latest('published_at')
                            ->paginate(6);

        return view('livewire.article-page', [
            'articles' => $articles
        ]);
    }
}
