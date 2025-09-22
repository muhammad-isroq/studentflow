<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Article;


class ShowArticle extends Component
{
    public Article $article;

    // Method 'mount' ini berjalan saat komponen dimuat
    public function mount(string $slug)
    {
        // Cari artikel berdasarkan slug, jika tidak ketemu akan error 404
        $this->article = Article::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.show-article')
            ->title($this->article->title); // Mengatur judul halaman browser
    }
}
